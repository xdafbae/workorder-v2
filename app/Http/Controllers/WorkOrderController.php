<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Symptom;
use App\Models\SystemNotification;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\RuleEngineService;
use App\Services\WorkOrderNumberService;
use App\Support\WorkOrderViewData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    public function create(): View
    {
        $device = Device::query()->with('unit')->where('status', '!=', 'inactive')->firstOrFail();
        $devices = Device::query()->with('unit')->where('status', '!=', 'inactive')->orderBy('name')->get();

        return view('workorder.create', [
            'title' => 'Laporkan Kerusakan',
            'role' => 'Perawat',
            'active' => 'report',
            'device' => WorkOrderViewData::device($device),
            'scannerDevices' => $devices->map(fn (Device $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'inventory_number' => $item->inventory_number,
                'serial_number' => $item->serial_number,
                'unit' => $item->unit?->name ?? '-',
                'status' => WorkOrderViewData::deviceStatusLabel($item->status),
                'barcode' => $item->barcode_code,
                'scan_codes' => collect([
                    $item->barcode_code,
                    $item->inventory_number,
                    $item->serial_number,
                    (string) $item->id,
                    str($item->inventory_number)->after('INV-')->toString(),
                ])->filter()->unique()->values(),
                'model' => $item->model,
            ])->values(),
            'symptomGroups' => Symptom::query()
                ->orderBy('category')
                ->orderBy('code')
                ->get(['id', 'code', 'name', 'category'])
                ->groupBy(fn ($symptom) => str($symptom->category)->headline()->toString())
                ->map(fn ($items) => $items->map(fn ($symptom) => [
                    'id' => $symptom->id,
                    'code' => $symptom->code,
                    'name' => $symptom->name,
                ])->values()->all())
                ->all(),
        ]);
    }

    public function store(Request $request, RuleEngineService $engine, WorkOrderNumberService $numbers): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'symptoms' => ['required', 'array', 'min:1'],
            'symptoms.*' => ['integer', 'exists:symptoms,id'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $workOrder = DB::transaction(function () use ($request, $data, $engine, $numbers) {
            $indications = $engine->evaluate($data['symptoms']);

            $workOrder = WorkOrder::query()->create([
                'wo_number' => $numbers->next(),
                'device_id' => $data['device_id'],
                'reporter_id' => $request->user()->id,
                'status' => 'pending',
                'description' => $data['description'] ?? null,
            ]);

            $workOrder->symptoms()->sync($data['symptoms']);
            $workOrder->indications()->sync($indications->mapWithKeys(fn ($indication) => [
                $indication->id => ['source' => 'system', 'score' => $indication->score ?? 0],
            ])->all());

            $workOrder->updates()->create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'notes' => 'Work Order dibuat dari laporan perawat.',
            ]);

            Device::query()->whereKey($data['device_id'])->update(['status' => 'repair']);

            User::query()->where('role', 'technician')->get()->each(function (User $user) use ($workOrder) {
                SystemNotification::query()->create([
                    'user_id' => $user->id,
                    'type' => 'work_order_created',
                    'data' => [
                        'wo_number' => $workOrder->wo_number,
                        'message' => 'Work Order baru menunggu penanganan.',
                    ],
                ]);
            });

            return $workOrder;
        });

        return redirect()->route('work-orders.show', $workOrder)->with('status', 'Work Order berhasil dibuat.');
    }

    public function show(WorkOrder $workOrder): View
    {
        $workOrder->load(['device.unit', 'reporter', 'technician', 'symptoms', 'indications.suggestions', 'updates.user']);

        return view('workorder.show', [
            'title' => $workOrder->wo_number,
            'role' => 'Teknisi Elektromedis',
            'active' => 'work-orders',
            'woNumber' => $workOrder->wo_number,
            'workOrderModel' => $workOrder,
            'device' => WorkOrderViewData::device($workOrder->device),
            'workOrders' => WorkOrderViewData::workOrders(collect([$workOrder])),
            'indications' => WorkOrderViewData::indications($workOrder->indications),
            'timeline' => $workOrder->updates->sortBy('created_at')->map(fn ($update) => [
                'time' => $update->created_at->format('H:i'),
                'actor' => $update->user?->name ?? 'Sistem',
                'status' => WorkOrderViewData::statusLabel($update->status),
                'notes' => $update->notes ?? $update->final_diagnosis ?? '-',
            ])->values()->all(),
        ]);
    }

    public function update(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed,closed'],
            'final_diagnosis' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photoPath = $request->file('photo')?->store('wo-updates', 'public');

        DB::transaction(function () use ($request, $workOrder, $data, $photoPath) {
            $workOrder->update([
                'status' => $data['status'],
                'technician_id' => $workOrder->technician_id ?: ($request->user()->isRole('technician') ? $request->user()->id : null),
            ] + $this->timestampUpdatesForStatus($workOrder, $data['status']));

            $this->syncDeviceStatus($workOrder->device_id);

            $workOrder->updates()->create([
                'user_id' => $request->user()->id,
                'status' => $data['status'],
                'final_diagnosis' => $data['final_diagnosis'] ?? null,
                'notes' => $data['notes'] ?? null,
                'photo_path' => $photoPath,
            ]);

            SystemNotification::query()->create([
                'user_id' => $workOrder->reporter_id,
                'type' => 'work_order_status_changed',
                'data' => [
                    'wo_number' => $workOrder->wo_number,
                    'status' => WorkOrderViewData::statusLabel($data['status']),
                ],
            ]);
        });

        return back()->with('status', 'Update Work Order tersimpan.');
    }

    private function timestampUpdatesForStatus(WorkOrder $workOrder, string $status): array
    {
        $now = now();

        return match ($status) {
            'pending' => [
                'processed_at' => null,
                'completed_at' => null,
                'closed_at' => null,
            ],
            'in_progress' => [
                'processed_at' => $workOrder->processed_at ?? $now,
                'completed_at' => null,
                'closed_at' => null,
            ],
            'completed' => [
                'processed_at' => $workOrder->processed_at ?? $now,
                'completed_at' => $workOrder->completed_at ?? $now,
                'closed_at' => null,
            ],
            'closed' => [
                'processed_at' => $workOrder->processed_at ?? $now,
                'completed_at' => $workOrder->completed_at ?? $now,
                'closed_at' => $workOrder->closed_at ?? $now,
            ],
        };
    }

    private function syncDeviceStatus(int $deviceId): void
    {
        $hasOpenWorkOrder = WorkOrder::query()
            ->where('device_id', $deviceId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->exists();

        Device::query()
            ->whereKey($deviceId)
            ->update(['status' => $hasOpenWorkOrder ? 'repair' : 'active']);
    }
}

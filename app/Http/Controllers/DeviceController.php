<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Unit;
use App\Models\WorkOrder;
use App\Support\WorkOrderViewData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{
    public function index(): View
    {
        $search = request('search');
        $status = request('status');

        $devices = Device::query()
            ->with(['unit', 'workOrders' => fn ($query) => $query->whereIn('status', ['pending', 'in_progress'])->latest()])
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('inventory_number', 'like', "%{$search}%")
                        ->orWhere('barcode_code', 'like', "%{$search}%")
                        ->orWhereHas('unit', fn ($unit) => $unit->where('name', 'like', "%{$search}%"));
                });
            })
            ->when(in_array($status, ['active', 'repair', 'inactive'], true), fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->get();

        $device = $devices->first();

        $deviceHistory = $device
            ? WorkOrderViewData::workOrders(WorkOrder::query()
                ->where('device_id', $device->id)
                ->with(['device.unit', 'reporter', 'technician', 'symptoms', 'indications.suggestions'])
                ->latest()
                ->take(5)
                ->get())
            : [];

        return view('devices.index', [
            'title' => 'Manajemen Alat',
            'role' => 'Admin',
            'active' => 'devices',
            'devices' => WorkOrderViewData::deviceRows($devices),
            'device' => $device ? WorkOrderViewData::device($device) : [],
            'deviceHistory' => $deviceHistory,
            'units' => Unit::query()->orderBy('name')->get(),
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function printQr(Request $request): View|Response
    {
        $devices = $this->qrLabelDevices();
        $renderMode = strtolower((string) $request->query('format', 'html'));

        if ($renderMode === 'pdf' || $request->boolean('download')) {
            $pdf = Pdf::loadView('devices.print-qr', [
                'devices' => $devices,
                'title' => 'Cetak Label QR',
                'renderMode' => 'pdf',
            ])->setPaper('a4');

            return $pdf->download('label-qr-alat.pdf');
        }

        return view('devices.print-qr', [
            'devices' => $devices,
            'title' => 'Cetak Label QR',
            'renderMode' => 'html',
        ]);
    }

    private function qrLabelDevices(): Collection
    {
        return Device::query()
            ->with('unit')
            ->orderBy('name')
            ->get()
            ->map(function (Device $device) {
                $qrSvg = QrCode::format('svg')
                    ->size(260)
                    ->margin(3)
                    ->generate($device->barcode_code);

                return [
                    'name' => $device->name,
                    'inventory_number' => $device->inventory_number,
                    'serial_number' => $device->serial_number,
                    'barcode_code' => $device->barcode_code,
                    'unit' => $device->unit?->name ?? '-',
                    'qr_svg' => $qrSvg,
                    'qr_image' => 'data:image/svg+xml;base64,'.base64_encode($qrSvg),
                ];
            });
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number'],
            'inventory_number' => ['required', 'string', 'max:255', 'unique:devices,inventory_number'],
            'status' => ['required', 'in:active,repair,inactive'],
            'purchased_at' => ['nullable', 'date'],
        ]);

        Device::query()->create($data);

        return back()->with('status', 'Data alat berhasil ditambahkan.');
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $data = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number,'.$device->id],
            'inventory_number' => ['required', 'string', 'max:255', 'unique:devices,inventory_number,'.$device->id],
            'barcode_code' => ['nullable', 'string', 'max:255', 'unique:devices,barcode_code,'.$device->id],
            'status' => ['required', 'in:active,repair,inactive'],
            'purchased_at' => ['nullable', 'date'],
        ]);

        if (blank($data['barcode_code'] ?? null)) {
            unset($data['barcode_code']);
        }

        $device->update($data);

        return back()->with('status', 'Data alat berhasil diperbarui.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        $device->delete();

        return back()->with('status', 'Data alat berhasil dihapus.');
    }
}

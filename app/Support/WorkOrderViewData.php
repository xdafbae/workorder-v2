<?php

namespace App\Support;

use App\Models\Device;
use App\Models\WorkOrder;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class WorkOrderViewData
{
    public static function statusLabel(?string $status): string
    {
        return [
            'pending' => 'Menunggu',
            'in_progress' => 'Diproses',
            'completed' => 'Selesai',
            'closed' => 'Ditutup',
        ][$status] ?? 'Menunggu';
    }

    public static function deviceStatusLabel(?string $status): string
    {
        return [
            'active' => 'Aktif',
            'repair' => 'Dalam Perbaikan',
            'inactive' => 'Non-Aktif',
        ][$status] ?? 'Aktif';
    }

    public static function severityLabel(?string $severity): string
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ][$severity] ?? 'Medium';
    }

    public static function date(?CarbonInterface $date): string
    {
        return $date ? $date->translatedFormat('d M Y, H:i') : '-';
    }

    public static function device(Device $device): array
    {
        return [
            'id' => $device->id,
            'name' => $device->name,
            'inventory_number' => $device->inventory_number,
            'serial_number' => $device->serial_number,
            'unit' => $device->unit?->name ?? '-',
            'status' => self::deviceStatusLabel($device->status),
            'raw_status' => $device->status,
            'barcode' => $device->barcode_code,
            'model' => $device->model,
            'purchased_at' => $device->purchased_at?->format('Y') ?? '-',
        ];
    }

    public static function deviceRows(Collection|EloquentCollection $devices): array
    {
        return $devices->map(fn (Device $device) => [
            'id' => $device->id,
            'name' => $device->name,
            'type' => $device->type,
            'model' => $device->model,
            'serial_number' => $device->serial_number,
            'inventory' => $device->inventory_number,
            'inventory_number' => $device->inventory_number,
            'barcode' => $device->barcode_code,
            'unit_id' => $device->unit_id,
            'unit' => $device->unit?->name ?? '-',
            'status' => self::deviceStatusLabel($device->status),
            'raw_status' => $device->status,
            'purchased_at' => $device->purchased_at?->format('Y-m-d'),
            'wo' => $device->workOrders->first()?->wo_number ?? '-',
        ])->values()->all();
    }

    public static function workOrders(Collection|EloquentCollection $workOrders): array
    {
        return $workOrders->map(function (WorkOrder $workOrder) {
            $severity = $workOrder->indications
                ->sortByDesc(fn ($item) => ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4][$item->severity] ?? 2)
                ->first()?->severity;

            return [
                'id' => $workOrder->id,
                'number' => $workOrder->wo_number,
                'device' => $workOrder->device?->name ?? '-',
                'unit' => $workOrder->device?->unit?->name ?? '-',
                'status' => self::statusLabel($workOrder->status),
                'raw_status' => $workOrder->status,
                'severity' => self::severityLabel($severity),
                'reporter' => $workOrder->reporter?->name ?? '-',
                'technician' => $workOrder->technician?->name ?? 'Belum ditugaskan',
                'created_at' => self::date($workOrder->created_at),
                'symptoms' => $workOrder->symptoms->pluck('name')->values()->all(),
            ];
        })->values()->all();
    }

    public static function indications(Collection|EloquentCollection $indications): array
    {
        return $indications->map(fn ($indication) => [
            'id' => $indication->id,
            'name' => $indication->name,
            'severity' => self::severityLabel($indication->severity),
            'weight' => $indication->pivot->score ?? $indication->score ?? 0,
            'suggestion' => $indication->suggestions->pluck('action_text')->first() ?? $indication->description,
        ])->values()->all();
    }

    public static function stats(): array
    {
        $today = WorkOrder::query()->whereDate('created_at', today())->count();
        $pending = WorkOrder::query()->where('status', 'pending')->count();
        $inProgress = WorkOrder::query()->where('status', 'in_progress')->count();
        $completedWeek = WorkOrder::query()
            ->whereIn('status', ['completed', 'closed'])
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        return [
            ['label' => 'WO hari ini', 'value' => (string) $today, 'trend' => 'real-time', 'tone' => 'emerald', 'icon' => 'clipboard-list'],
            ['label' => 'Menunggu', 'value' => (string) $pending, 'trend' => 'SLA 2 jam', 'tone' => 'amber', 'icon' => 'timer'],
            ['label' => 'Diproses', 'value' => (string) $inProgress, 'trend' => 'teknisi aktif', 'tone' => 'sky', 'icon' => 'wrench'],
            ['label' => 'Selesai', 'value' => (string) $completedWeek, 'trend' => 'minggu ini', 'tone' => 'slate', 'icon' => 'check-circle'],
        ];
    }
}

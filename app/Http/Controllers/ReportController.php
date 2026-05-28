<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Unit;
use App\Models\WorkOrder;
use App\Support\WorkOrderViewData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $workOrders = $this->filteredWorkOrders($request)
            ->with(['symptoms', 'indications.suggestions'])
            ->latest()
            ->get();

        return view('reports.index', [
            'title' => 'Laporan Work Order',
            'role' => 'Supervisor / Admin',
            'active' => 'reports',
            'workOrders' => WorkOrderViewData::workOrders($workOrders),
            'units' => Unit::query()->orderBy('name')->get(),
            'summary' => [
                'total' => $workOrders->count(),
                'completed' => $workOrders->whereIn('status', ['completed', 'closed'])->count(),
                'pending' => $workOrders->where('status', 'pending')->count(),
                'over_sla' => $workOrders->where('status', 'pending')->filter(fn ($wo) => $wo->created_at->lt(now()->subHours(2)))->count(),
            ],
            'topDevices' => Device::query()->withCount('workOrders')->orderByDesc('work_orders_count')->take(5)->get(),
        ]);
    }

    public function exportCsv(Request $request): Response
    {
        $workOrders = $this->filteredWorkOrders($request)
            ->latest()
            ->get();

        $csv = collect([
            ['Nomor WO', 'Alat', 'Ruangan', 'Pelapor', 'Teknisi', 'Status', 'Tanggal'],
        ])->merge($workOrders->map(fn (WorkOrder $workOrder) => [
            $workOrder->wo_number,
            $workOrder->device?->name,
            $workOrder->device?->unit?->name,
            $workOrder->reporter?->name,
            $workOrder->technician?->name ?? 'Belum ditugaskan',
            WorkOrderViewData::statusLabel($workOrder->status),
            WorkOrderViewData::date($workOrder->created_at),
        ]))->map(fn ($row) => implode(',', array_map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"', $row)))->join("\n");

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-work-order.csv"',
        ]);
    }

    private function filteredWorkOrders(Request $request)
    {
        $query = WorkOrder::query()->with(['device.unit', 'reporter', 'technician']);

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date('date'));
        }

        if ($request->filled('unit_id')) {
            $query->whereHas('device', fn ($device) => $device->where('unit_id', $request->integer('unit_id')));
        }

        return $query;
    }
}

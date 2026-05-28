<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\WorkOrder;
use App\Support\WorkOrderViewData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function nurse(Request $request): View
    {
        $workOrders = WorkOrder::query()
            ->where('reporter_id', $request->user()->id)
            ->with(['device.unit', 'reporter', 'technician', 'symptoms', 'indications.suggestions'])
            ->latest()
            ->take(8)
            ->get();

        $device = Device::query()->with('unit')->firstOrFail();

        return view('dashboard.perawat', [
            'title' => 'Dashboard Perawat',
            'role' => 'Perawat',
            'active' => 'dashboard',
            'stats' => WorkOrderViewData::stats(),
            'device' => WorkOrderViewData::device($device),
            'workOrders' => WorkOrderViewData::workOrders($workOrders),
        ]);
    }

    public function technician(): View
    {
        $workOrders = WorkOrder::query()
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['device.unit', 'reporter', 'technician', 'symptoms', 'indications.suggestions'])
            ->latest()
            ->get();

        $indications = $workOrders
            ->flatMap->indications
            ->unique('id')
            ->take(4);

        return view('dashboard.teknisi', [
            'title' => 'Dashboard Teknisi',
            'role' => 'Teknisi Elektromedis',
            'active' => 'technician',
            'stats' => WorkOrderViewData::stats(),
            'workOrders' => WorkOrderViewData::workOrders($workOrders),
            'indications' => WorkOrderViewData::indications($indications),
        ]);
    }

    public function admin(): View
    {
        $workOrders = WorkOrder::query()
            ->with(['device.unit', 'reporter', 'technician', 'symptoms', 'indications.suggestions'])
            ->latest()
            ->take(10)
            ->get();

        $devices = Device::query()
            ->with(['unit', 'workOrders' => fn ($query) => $query->whereIn('status', ['pending', 'in_progress'])->latest()])
            ->get();

        return view('dashboard.admin', [
            'title' => 'Dashboard Admin',
            'role' => 'Supervisor / Admin',
            'active' => 'admin',
            'stats' => WorkOrderViewData::stats(),
            'workOrders' => WorkOrderViewData::workOrders($workOrders),
            'devices' => WorkOrderViewData::deviceRows($devices),
        ]);
    }
}

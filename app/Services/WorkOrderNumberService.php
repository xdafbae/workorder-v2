<?php

namespace App\Services;

use App\Models\WorkOrder;

class WorkOrderNumberService
{
    public function next(): string
    {
        $prefix = 'WO-'.now()->format('Y');
        $latest = WorkOrder::query()
            ->where('wo_number', 'like', $prefix.'-%')
            ->pluck('wo_number')
            ->map(fn (string $number) => (int) substr($number, -4))
            ->max();

        $sequence = $latest ? $latest + 1 : 1;

        return sprintf('%s-%04d', $prefix, $sequence);
    }
}

@extends('layouts.app')

@section('content')
<section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Laporan</p>
            <h3 class="mt-1 text-xl font-bold text-slate-950">Rekap Work Order</h3>
        </div>
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col gap-2 sm:flex-row">
            <input name="date" type="date" value="{{ request('date', now()->toDateString()) }}" class="rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
            <select name="unit_id" class="rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                <option value="">Semua ruangan</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" @selected((string) request('unit_id') === (string) $unit->id)>{{ $unit->name }}</option>
                @endforeach
            </select>
            <button class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="filter" class="h-4 w-4"></i>
                Filter
            </button>
            <a href="{{ route('reports.export-csv') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-cyan-700 px-3 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                <i data-lucide="download" class="h-4 w-4"></i>
                Export CSV
            </a>
        </form>
    </div>
</section>

<div class="mt-6 grid gap-6 xl:grid-cols-[.9fr_1.1fr]">
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Ringkasan periode</p>
        <div class="mt-5 grid grid-cols-2 gap-3">
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Total WO</p>
                <p class="mt-2 text-3xl font-bold text-slate-950">{{ $summary['total'] }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Selesai</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $summary['completed'] }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Pending</p>
                <p class="mt-2 text-3xl font-bold text-amber-700">{{ $summary['pending'] }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Over SLA</p>
                <p class="mt-2 text-3xl font-bold text-red-700">{{ $summary['over_sla'] }}</p>
            </div>
        </div>

        <div class="mt-5 rounded-lg border border-slate-200 p-4">
            <p class="text-sm font-semibold text-slate-950">Rata-rata waktu penanganan</p>
            <div class="mt-4 h-2 rounded-full bg-slate-100">
                <div class="h-2 rounded-full bg-cyan-600" style="width: 72%"></div>
            </div>
            <div class="mt-2 flex justify-between text-xs text-slate-500">
                <span>0 jam</span>
                <span class="font-semibold text-slate-950">4.2 jam</span>
                <span>6 jam</span>
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Data WO</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Tabel laporan</h3>
            </div>
            <a href="{{ route('reports.export-csv') }}" class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                CSV
            </a>
        </div>

        <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">WO</th>
                            <th class="px-4 py-3">Alat</th>
                            <th class="px-4 py-3">Ruangan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Teknisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($workOrders as $wo)
                            <tr>
                                <td class="px-4 py-4 font-semibold text-slate-950">{{ $wo['number'] }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $wo['device'] }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $wo['unit'] }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $wo['status'] }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $wo['technician'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

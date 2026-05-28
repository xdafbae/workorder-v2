@extends('layouts.app')

@section('content')
@php
    $queue = collect($workOrders)->whereIn('status', ['Menunggu', 'Diproses'])->values();
    $severityClass = [
        'High' => 'bg-red-50 text-red-700',
        'Medium' => 'bg-amber-50 text-amber-700',
        'Low' => 'bg-emerald-50 text-emerald-700',
    ];
@endphp

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($stats as $stat)
        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stat['value'] }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-{{ $stat['tone'] }}-50 text-{{ $stat['tone'] }}-700">
                    <i data-lucide="{{ $stat['icon'] }}" class="h-5 w-5"></i>
                </div>
            </div>
            <p class="mt-3 text-sm font-medium text-slate-500">{{ $stat['trend'] }}</p>
        </section>
    @endforeach
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Antrian teknisi</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">WO perlu ditangani</h3>
            </div>
            <div class="flex gap-2">
                <button class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i data-lucide="filter" class="h-4 w-4"></i>
                    Filter
                </button>
                <button class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i data-lucide="bell" class="h-4 w-4"></i>
                    Notifikasi
                </button>
            </div>
        </div>

        <div class="mt-5 space-y-3">
            @foreach ($queue as $wo)
                <article class="rounded-lg border border-slate-200 p-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="/work-orders/{{ $wo['number'] }}" class="font-bold text-slate-950 hover:text-cyan-700">{{ $wo['number'] }}</a>
                                <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $severityClass[$wo['severity']] ?? 'bg-slate-100 text-slate-700' }}">{{ $wo['severity'] }}</span>
                                <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">{{ $wo['status'] }}</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ $wo['device'] }} · {{ $wo['unit'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Pelapor: {{ $wo['reporter'] }} · {{ $wo['created_at'] }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="/work-orders/{{ $wo['number'] }}" class="inline-flex items-center gap-2 rounded-md bg-cyan-700 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                                Detail
                            </a>
                            @if (($wo['raw_status'] ?? '') === 'pending')
                                <form method="POST" action="{{ route('work-orders.update', $wo['number']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <input type="hidden" name="notes" value="Teknisi mengambil Work Order untuk diproses.">
                                    <button class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50" aria-label="Ambil WO" title="Ambil WO">
                                        <i data-lucide="user-check" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-sky-50 text-sky-700 ring-1 ring-sky-100" aria-label="Sedang diproses" title="Sedang diproses">
                                    <i data-lucide="user-check" class="h-4 w-4"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Prioritas awal</p>
        <h3 class="mt-1 text-xl font-bold text-slate-950">Indikasi dan saran</h3>

        <div class="mt-5 space-y-4">
            @foreach ($indications as $item)
                <div class="rounded-lg border border-slate-200 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-bold text-slate-950">{{ $item['name'] }}</p>
                        <span class="rounded-md bg-cyan-50 px-2 py-1 text-xs font-semibold text-cyan-700">Bobot {{ $item['weight'] }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-500">{{ $item['suggestion'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

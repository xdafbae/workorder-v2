@extends('layouts.app')

@section('content')
@php
    $statusClass = [
        'Menunggu' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'Diproses' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'Selesai' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'Ditutup' => 'bg-slate-100 text-slate-700 ring-slate-200',
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

<div class="mt-6 grid gap-6 xl:grid-cols-[.9fr_1.1fr]">
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Pelaporan cepat</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Scan QR alat dan pilih gejala</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Data alat, indikasi awal, dan saran perbaikan tampil sebelum laporan dikirim.</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-50 text-cyan-700">
                <i data-lucide="scan-line" class="h-6 w-6"></i>
            </div>
        </div>

        <div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-950">{{ $device['name'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $device['inventory_number'] }} · {{ $device['unit'] }}</p>
                </div>
                <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">{{ $device['status'] }}</span>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                <div class="rounded-md bg-white p-3">
                    <p class="text-xs text-slate-500">Barcode</p>
                    <p class="mt-1 text-sm font-bold text-slate-950">{{ $device['barcode'] }}</p>
                </div>
                <div class="rounded-md bg-white p-3">
                    <p class="text-xs text-slate-500">Model</p>
                    <p class="mt-1 text-sm font-bold text-slate-950">{{ $device['model'] }}</p>
                </div>
                <div class="rounded-md bg-white p-3">
                    <p class="text-xs text-slate-500">Tahun</p>
                    <p class="mt-1 text-sm font-bold text-slate-950">{{ $device['purchased_at'] }}</p>
                </div>
            </div>
        </div>

        <a href="/work-orders/create" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-3 text-sm font-semibold text-white hover:bg-cyan-800">
            <i data-lucide="plus-circle" class="h-4 w-4"></i>
            Laporkan Kerusakan Baru
        </a>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Work Order saya</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Riwayat laporan terbaru</h3>
            </div>
            <a href="/reports" class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="file-text" class="h-4 w-4"></i>
                Laporan
            </a>
        </div>

        <div class="mt-5 space-y-3">
            @foreach ($workOrders as $wo)
                <article class="rounded-lg border border-slate-200 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <a href="/work-orders/{{ $wo['number'] }}" class="font-bold text-slate-950 hover:text-cyan-700">{{ $wo['number'] }}</a>
                            <p class="mt-1 text-sm text-slate-500">{{ $wo['device'] }} · {{ $wo['unit'] }}</p>
                        </div>
                        <span class="w-fit rounded-md px-2 py-1 text-xs font-semibold ring-1 {{ $statusClass[$wo['status']] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $wo['status'] }}</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($wo['symptoms'] as $symptom)
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">{{ $symptom }}</span>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection

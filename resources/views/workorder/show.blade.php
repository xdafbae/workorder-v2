@extends('layouts.app')

@section('content')
@php
    $wo = $workOrders[0];
@endphp

<div class="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-2xl font-bold text-slate-950">{{ $woNumber ?? $wo['number'] }}</h3>
                        <span class="rounded-md bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700">{{ $wo['status'] }}</span>
                        <span class="rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">{{ $wo['severity'] }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">{{ $wo['created_at'] }} - {{ $wo['reporter'] }}</p>
                </div>
                <div class="flex gap-2">
                    <button class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <i data-lucide="printer" class="h-4 w-4"></i>
                        Cetak
                    </button>
                    @if (($wo['raw_status'] ?? '') === 'pending')
                        <form method="POST" action="{{ route('work-orders.update', $workOrderModel) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="in_progress">
                            <input type="hidden" name="notes" value="Teknisi mengambil Work Order untuk diproses.">
                            <button class="inline-flex items-center gap-2 rounded-md bg-cyan-700 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                                <i data-lucide="user-check" class="h-4 w-4"></i>
                                Ambil WO
                            </button>
                        </form>
                    @elseif (($wo['raw_status'] ?? '') === 'in_progress')
                        <span class="inline-flex items-center gap-2 rounded-md bg-sky-50 px-3 py-2 text-sm font-semibold text-sky-700 ring-1 ring-sky-100">
                            <i data-lucide="user-check" class="h-4 w-4"></i>
                            Sedang Diproses
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-500">Alat</p>
                    <p class="mt-1 font-bold text-slate-950">{{ $wo['device'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $device['inventory_number'] }} - {{ $device['serial_number'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-500">Lokasi</p>
                    <p class="mt-1 font-bold text-slate-950">{{ $wo['unit'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Teknisi: {{ $wo['technician'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Indikasi sistem</p>
            <h3 class="mt-1 text-xl font-bold text-slate-950">Hasil rule-based engine</h3>

            <div class="mt-5 space-y-3">
                @foreach ($indications as $item)
                    <article class="rounded-lg border border-slate-200 p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-bold text-slate-950">{{ $item['name'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">Severity: {{ $item['severity'] }}</p>
                            </div>
                            <span class="w-fit rounded-md bg-cyan-50 px-2 py-1 text-xs font-semibold text-cyan-700">Bobot {{ $item['weight'] }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $item['suggestion'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Gejala dipilih</p>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($wo['symptoms'] as $symptom)
                    <span class="rounded-md bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700">{{ $symptom }}</span>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Update teknisi</p>
            <h3 class="mt-1 text-xl font-bold text-slate-950">Status dan catatan</h3>
            <form method="POST" action="{{ route('work-orders.update', $workOrderModel) }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Status WO</span>
                    <select name="status" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        <option value="pending" @selected(($wo['raw_status'] ?? '') === 'pending')>Menunggu</option>
                        <option value="in_progress" @selected(($wo['raw_status'] ?? '') === 'in_progress')>Diproses</option>
                        <option value="completed" @selected(($wo['raw_status'] ?? '') === 'completed')>Selesai</option>
                        <option value="closed" @selected(($wo['raw_status'] ?? '') === 'closed')>Ditutup</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Diagnosis akhir</span>
                    <input name="final_diagnosis" type="text" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100" value="{{ $indications[0]['name'] ?? '' }}">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Catatan teknis</span>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">Pemeriksaan awal perlu difokuskan pada adaptor, fuse, dan konektor internal.</textarea>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Foto hasil perbaikan</span>
                    <input name="photo" type="file" accept="image/*" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-3 text-sm font-semibold text-white hover:bg-cyan-800">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Simpan Update
                </button>
            </form>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Timeline</p>
            <div class="mt-5 space-y-4">
                @foreach ($timeline as $event)
                    <div class="flex gap-3">
                        <div class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-cyan-50 text-cyan-700">
                            <i data-lucide="clock-3" class="h-4 w-4"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-950">{{ $event['status'] }} - {{ $event['time'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $event['actor'] }}: {{ $event['notes'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection

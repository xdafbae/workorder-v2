@extends('layouts.app')

@section('content')
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

<div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_.8fr]">
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Analitik WO</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Kerusakan berdasarkan kategori</h3>
            </div>
            <a href="/reports" class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="download" class="h-4 w-4"></i>
                Export
            </a>
        </div>

        <div class="mt-6 space-y-4">
            @foreach ([['Power Check', 88, 'bg-red-500'], ['Alarm Check', 64, 'bg-amber-500'], ['Performa Check', 52, 'bg-cyan-600'], ['Sensor Check', 41, 'bg-emerald-500'], ['Mekanik dan Motor', 35, 'bg-slate-500'], ['Software Check', 24, 'bg-indigo-500']] as $bar)
                <div>
                    <div class="mb-2 flex items-center justify-between text-sm">
                        <span class="font-medium text-slate-700">{{ $bar[0] }}</span>
                        <span class="font-semibold text-slate-950">{{ $bar[1] }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100">
                        <div class="h-2 rounded-full {{ $bar[2] }}" style="width: {{ $bar[1] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">SLA teknisi</p>
        <h3 class="mt-1 text-xl font-bold text-slate-950">Kinerja minggu ini</h3>

        <div class="mt-5 grid grid-cols-2 gap-3">
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Rata-rata respon</p>
                <p class="mt-2 text-2xl font-bold text-slate-950">38m</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Rata-rata selesai</p>
                <p class="mt-2 text-2xl font-bold text-slate-950">4.2j</p>
            </div>
        </div>

        <div class="mt-5 space-y-3">
            @foreach ($devices as $item)
                <div class="flex items-center justify-between rounded-lg border border-slate-200 p-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-950">{{ $item['name'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $item['unit'] }}</p>
                    </div>
                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">{{ $item['status'] }}</span>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

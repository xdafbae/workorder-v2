@extends('layouts.app')

@section('content')
<div class="grid gap-6 xl:grid-cols-[1fr_360px]">
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Master alat</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Infusion Pump dan Syringe Pump</h3>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('devices.print-qr') }}" target="_blank" class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i data-lucide="qr-code" class="h-4 w-4"></i>
                    Cetak QR
                </a>
                <button type="button" data-open-device-modal class="inline-flex items-center gap-2 rounded-md bg-cyan-700 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Tambah Alat
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('devices.index') }}" class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
            <label class="relative block">
                <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input name="search" value="{{ $filters['search'] }}" type="search" placeholder="Cari nama, serial, nomor inventaris, ruangan" class="w-full rounded-md border border-slate-300 py-2.5 pl-9 pr-3 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
            </label>
            <select name="status" class="rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                <option value="">Semua status</option>
                <option value="active" @selected($filters['status'] === 'active')>Aktif</option>
                <option value="repair" @selected($filters['status'] === 'repair')>Dalam Perbaikan</option>
                <option value="inactive" @selected($filters['status'] === 'inactive')>Non-Aktif</option>
            </select>
            <button class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="filter" class="h-4 w-4"></i>
                Filter
            </button>
        </form>

        <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Alat</th>
                            <th class="px-4 py-3">Inventaris</th>
                            <th class="px-4 py-3">Ruangan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">WO Aktif</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($devices as $item)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-slate-950">{{ $item['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $item['type'] }}{{ $item['model'] ? ' - '.$item['model'] : '' }}</p>
                                </td>
                                <td class="px-4 py-4 text-slate-600">{{ $item['inventory'] }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $item['unit'] }}</td>
                                <td class="px-4 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $item['raw_status'] === 'active' ? 'bg-emerald-50 text-emerald-700' : ($item['raw_status'] === 'inactive' ? 'bg-slate-100 text-slate-600' : 'bg-amber-50 text-amber-700') }}">{{ $item['status'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-slate-600">{{ $item['wo'] }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                    <button type="button"
                                        data-edit-device
                                        data-id="{{ $item['id'] }}"
                                        data-name="{{ $item['name'] }}"
                                        data-type="{{ $item['type'] }}"
                                        data-model="{{ $item['model'] }}"
                                        data-serial-number="{{ $item['serial_number'] }}"
                                        data-inventory-number="{{ $item['inventory_number'] }}"
                                        data-barcode="{{ $item['barcode'] }}"
                                        data-unit-id="{{ $item['unit_id'] }}"
                                        data-status="{{ $item['raw_status'] }}"
                                        data-purchased-at="{{ $item['purchased_at'] }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50" aria-label="Edit alat">
                                        <i data-lucide="pencil" class="h-4 w-4"></i>
                                    </button>
                                    <form method="POST" action="{{ route('devices.destroy', $item['id']) }}" data-delete-device>
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-red-100 text-red-600 hover:bg-red-50" aria-label="Hapus alat">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm font-medium text-slate-500">Data alat tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Label QR</p>
            @if ($device)
                <h3 class="mt-1 text-xl font-bold text-slate-950">{{ $device['barcode'] }}</h3>
                <div class="mt-5 mx-auto grid h-48 w-48 grid-cols-6 gap-1 rounded-lg border border-slate-200 bg-white p-3">
                    @for ($i = 0; $i < 36; $i++)
                        <span class="{{ in_array($i, [0,1,2,6,8,12,13,14,20,24,25,26,5,11,17,23,29,35,30,31,32,4,9,15,19,28,33]) ? 'bg-slate-950' : 'bg-white' }}"></span>
                    @endfor
                </div>
                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Nama</dt>
                        <dd class="font-semibold text-slate-950">{{ $device['name'] }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Inventaris</dt>
                        <dd class="font-semibold text-slate-950">{{ $device['inventory_number'] }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Lokasi</dt>
                        <dd class="font-semibold text-slate-950">{{ $device['unit'] }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-3 text-sm text-slate-500">Belum ada alat yang cocok dengan filter.</p>
            @endif
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Histori</p>
            <div class="mt-4 space-y-3">
                @forelse ($deviceHistory as $wo)
                    <a href="{{ route('work-orders.show', $wo['number']) }}" class="block rounded-md bg-slate-50 p-3 hover:bg-slate-100">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-950">{{ $wo['number'] }}</p>
                            <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $wo['raw_status'] === 'pending' ? 'bg-amber-50 text-amber-700' : ($wo['raw_status'] === 'in_progress' ? 'bg-sky-50 text-sky-700' : 'bg-emerald-50 text-emerald-700') }}">
                                {{ $wo['status'] }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $wo['severity'] }} - {{ $wo['created_at'] }}</p>
                    </a>
                @empty
                    <p class="rounded-md bg-slate-50 p-3 text-sm font-medium text-slate-500">Belum ada Work Order untuk alat ini.</p>
                @endforelse
            </div>
        </section>
    </aside>
</div>

<div id="deviceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <div>
                <p id="deviceModalEyebrow" class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Tambah alat</p>
                <h3 id="deviceModalTitle" class="mt-1 text-xl font-bold text-slate-950">Data alat baru</h3>
            </div>
            <button type="button" data-close-device-modal class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50" aria-label="Tutup modal">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        <form id="deviceForm" method="POST" action="{{ route('devices.store') }}" class="space-y-4 px-5 py-5">
            @csrf
            <input id="deviceFormMethod" type="hidden" name="_method" value="POST" disabled>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-slate-700">Nama alat</span>
                    <input id="deviceNameInput" name="name" required value="{{ old('name') }}" placeholder="Nama alat" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Tipe</span>
                    <input id="deviceTypeInput" name="type" required value="{{ old('type', 'Syringe Pump') }}" placeholder="Tipe" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Model</span>
                    <input id="deviceModelInput" name="model" value="{{ old('model') }}" placeholder="Model" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Serial number</span>
                    <input id="deviceSerialInput" name="serial_number" required value="{{ old('serial_number') }}" placeholder="Serial number" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Nomor inventaris</span>
                    <input id="deviceInventoryInput" name="inventory_number" required value="{{ old('inventory_number') }}" placeholder="Nomor inventaris" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Ruangan</span>
                    <select id="deviceUnitInput" name="unit_id" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected((string) old('unit_id') === (string) $unit->id)>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Status</span>
                    <select id="deviceStatusInput" name="status" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        <option value="active" @selected(old('status', 'active') === 'active')>Aktif</option>
                        <option value="repair" @selected(old('status') === 'repair')>Dalam Perbaikan</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Non-Aktif</option>
                    </select>
                </label>

                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-slate-700">Tanggal pembelian</span>
                    <input id="devicePurchasedInput" name="purchased_at" type="date" value="{{ old('purchased_at') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <label id="deviceBarcodeField" class="hidden md:col-span-2">
                    <span class="text-sm font-semibold text-slate-700">Kode barcode</span>
                    <input id="deviceBarcodeInput" name="barcode_code" value="{{ old('barcode_code') }}" placeholder="Kosongkan jika tidak ingin mengubah barcode" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>
            </div>

            <div id="barcodeHelp" class="rounded-md border border-dashed border-cyan-200 bg-cyan-50 px-3 py-2.5 text-sm font-semibold text-cyan-800">
                Kode barcode dibuat otomatis secara acak saat disimpan.
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4">
                <button type="button" data-close-device-modal class="inline-flex items-center justify-center rounded-md border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button id="deviceSubmitButton" class="inline-flex items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Simpan Alat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const deviceModal = document.getElementById('deviceModal');
    const deviceForm = document.getElementById('deviceForm');
    const deviceFormMethod = document.getElementById('deviceFormMethod');
    const deviceModalEyebrow = document.getElementById('deviceModalEyebrow');
    const deviceModalTitle = document.getElementById('deviceModalTitle');
    const deviceNameInput = document.getElementById('deviceNameInput');
    const deviceTypeInput = document.getElementById('deviceTypeInput');
    const deviceModelInput = document.getElementById('deviceModelInput');
    const deviceSerialInput = document.getElementById('deviceSerialInput');
    const deviceInventoryInput = document.getElementById('deviceInventoryInput');
    const deviceUnitInput = document.getElementById('deviceUnitInput');
    const deviceStatusInput = document.getElementById('deviceStatusInput');
    const devicePurchasedInput = document.getElementById('devicePurchasedInput');
    const deviceBarcodeField = document.getElementById('deviceBarcodeField');
    const deviceBarcodeInput = document.getElementById('deviceBarcodeInput');
    const barcodeHelp = document.getElementById('barcodeHelp');
    const deviceSubmitButton = document.getElementById('deviceSubmitButton');
    const storeDeviceUrl = @json(route('devices.store'));
    const updateDeviceUrl = @json(route('devices.update', ['device' => '__ID__']));

    const openDeviceModal = () => {
        deviceModal.classList.remove('hidden');
        deviceModal.classList.add('flex');
    };
    const closeDeviceModal = () => {
        deviceModal.classList.add('hidden');
        deviceModal.classList.remove('flex');
    };

    const setFormMode = (mode, device = {}) => {
        const isEdit = mode === 'edit';

        deviceForm.action = isEdit ? updateDeviceUrl.replace('__ID__', device.id) : storeDeviceUrl;
        deviceFormMethod.disabled = !isEdit;
        deviceFormMethod.value = isEdit ? 'PATCH' : 'POST';
        deviceModalEyebrow.textContent = isEdit ? 'Edit alat' : 'Tambah alat';
        deviceModalTitle.textContent = isEdit ? device.name : 'Data alat baru';
        deviceSubmitButton.innerHTML = `<i data-lucide="save" class="h-4 w-4"></i> ${isEdit ? 'Update Alat' : 'Simpan Alat'}`;
        deviceNameInput.value = device.name || '';
        deviceTypeInput.value = device.type || 'Syringe Pump';
        deviceModelInput.value = device.model || '';
        deviceSerialInput.value = device.serialNumber || '';
        deviceInventoryInput.value = device.inventoryNumber || '';
        deviceUnitInput.value = device.unitId || deviceUnitInput.options[0]?.value || '';
        deviceStatusInput.value = device.status || 'active';
        devicePurchasedInput.value = device.purchasedAt || '';
        deviceBarcodeInput.value = device.barcode || '';
        deviceBarcodeField.classList.toggle('hidden', !isEdit);
        barcodeHelp.textContent = isEdit
            ? 'Kode barcode dapat diubah, tetapi harus unik. Label QR lama perlu dicetak ulang jika kode berubah.'
            : 'Kode barcode dibuat otomatis secara acak saat disimpan.';
        lucide.createIcons();
    };

    document.querySelectorAll('[data-open-device-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            setFormMode('create');
            openDeviceModal();
        });
    });

    document.querySelectorAll('[data-edit-device]').forEach((button) => {
        button.addEventListener('click', () => {
            setFormMode('edit', button.dataset);
            openDeviceModal();
        });
    });

    document.querySelectorAll('[data-delete-device]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!confirm('Hapus data alat ini? Work order terkait juga akan ikut terhapus.')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-close-device-modal]').forEach((button) => {
        button.addEventListener('click', closeDeviceModal);
    });

    deviceModal.addEventListener('click', (event) => {
        if (event.target === deviceModal) {
            closeDeviceModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !deviceModal.classList.contains('hidden')) {
            closeDeviceModal();
        }
    });

    @if ($errors->any())
        openDeviceModal();
    @endif
</script>
@endsection

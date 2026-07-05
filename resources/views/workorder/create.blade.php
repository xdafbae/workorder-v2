@extends('layouts.app')

@section('content')
<div class="grid gap-6 xl:grid-cols-[.95fr_1.05fr]">
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Scan barcode</p>
                    <h3 class="mt-1 text-xl font-bold text-slate-950">Identifikasi alat</h3>
                </div>
                <button id="scanButton" type="button" class="inline-flex items-center gap-2 rounded-md bg-cyan-700 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                    <i data-lucide="camera" class="h-4 w-4"></i>
                    Mulai Scan
                </button>
            </div>

            <div class="scan-frame mt-5 rounded-lg border border-slate-800 p-5 text-white">
                <div id="scannerReader" class="hidden overflow-hidden rounded-lg bg-slate-950"></div>
                <div id="scannerPlaceholder" class="mx-auto w-full max-w-xs rounded-lg border border-cyan-300 bg-slate-950/70 p-5 text-center">
                    <div class="mx-auto grid h-32 w-32 grid-cols-5 gap-1 rounded-md bg-white p-2" aria-hidden="true">
                        @for ($i = 0; $i < 25; $i++)
                            <span class="{{ in_array($i, [0,1,2,5,7,10,11,12,17,20,21,22,4,9,14,19,24,18,6]) ? 'bg-slate-950' : 'bg-white' }}"></span>
                        @endfor
                    </div>
                    <p class="mt-1 text-xs text-slate-300">QR saat ini: {{ $device['barcode'] }}</p>
                </div>
                <p id="scanStatus" class="mt-4 rounded-md bg-slate-950/80 px-3 py-2 text-center text-sm font-semibold text-cyan-50">
                    Arahkan kamera ke QR label alat.
                </p>
            </div>

            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                <label for="manualScanInput" class="text-sm font-semibold text-slate-700">Kode label alat</label>
                <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                    <input id="manualScanInput" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100" placeholder="Contoh: WO-QR-..., INV-IP-HCU-019, atau SN-...">
                    <button id="manualScanButton" type="button" class="inline-flex items-center justify-center gap-2 rounded-md border border-cyan-200 bg-white px-3 py-2.5 text-sm font-semibold text-cyan-700 hover:bg-cyan-50">
                        <i data-lucide="search" class="h-4 w-4"></i>
                        Cek Kode
                    </button>
                </div>
                <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                    <input id="imageScanInput" type="file" accept="image/*" capture="environment" class="sr-only">
                    <label for="imageScanInput" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                        <i data-lucide="image-up" class="h-4 w-4"></i>
                        Baca dari Foto QR
                    </label>
                </div>
            </div>
        </div>

        <div id="deviceCard" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Data alat</p>
                    <h3 id="deviceName" class="mt-1 text-xl font-bold text-slate-950">{{ $device['name'] }}</h3>
                    <p id="deviceMeta" class="mt-2 text-sm text-slate-500">{{ $device['inventory_number'] }} - {{ $device['unit'] }}</p>
                </div>
                <span id="deviceStatus" class="rounded-md bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $device['status'] }}</span>
            </div>

            <div id="devicePhotoWrapper" class="mt-4 {{ $device['photo_url'] ? '' : 'hidden' }}">
                <img id="devicePhoto" src="{{ $device['photo_url'] ?? '#' }}" alt="Foto Alat" class="h-48 w-full object-cover rounded-lg border border-slate-200">
            </div>

            <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Serial Number</dt>
                    <dd id="deviceSerial" class="mt-1 font-semibold text-slate-950">{{ $device['serial_number'] }}</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Model</dt>
                    <dd id="deviceModel" class="mt-1 font-semibold text-slate-950">{{ $device['model'] }}</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Lokasi</dt>
                    <dd id="deviceUnit" class="mt-1 font-semibold text-slate-950">{{ $device['unit'] }}</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Barcode</dt>
                    <dd id="deviceBarcode" class="mt-1 font-semibold text-slate-950">{{ $device['barcode'] }}</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Tanggal Pembelian</dt>
                    <dd id="devicePurchased" class="mt-1 font-semibold text-slate-950">{{ $device['purchased_formatted'] }}</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Terakhir Maintenance</dt>
                    <dd id="deviceLastMaintenance" class="mt-1 font-semibold text-emerald-700">{{ $device['last_maintenance_formatted'] }}</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3 sm:col-span-2">
                    <dt class="text-slate-500">Terakhir Kalibrasi</dt>
                    <dd id="deviceLastCalibration" class="mt-1 font-semibold text-cyan-700">{{ $device['last_calibration_formatted'] }}</dd>
                </div>
            </dl>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Gejala kerusakan</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Checklist pemeriksaan awal</h3>
            </div>
            <span id="selectedCounter" class="rounded-md bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600">0 dipilih</span>
        </div>

        <form method="POST" action="{{ route('work-orders.store') }}" class="mt-5 space-y-5">
            @csrf
            <input id="deviceIdInput" type="hidden" name="device_id" value="{{ $device['id'] }}">
            @foreach ($symptomGroups as $group => $symptoms)
                <fieldset class="rounded-lg border border-slate-200 p-4">
                    <legend class="px-2 text-sm font-bold text-slate-950">{{ $group }}</legend>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($symptoms as $symptom)
                            <label class="flex cursor-pointer items-start gap-3 rounded-md border border-slate-200 p-3 hover:border-cyan-300 hover:bg-cyan-50/40">
                                <input type="checkbox" name="symptoms[]" value="{{ $symptom['id'] }}" data-code="{{ $symptom['code'] }}" data-name="{{ $symptom['name'] }}" class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                <span>
                                    <span class="block text-sm font-semibold text-slate-950">{{ $symptom['name'] }}</span>
                                    <span class="block text-xs text-slate-500">{{ $symptom['code'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Keterangan tambahan</span>
                <textarea name="description" rows="4" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100" placeholder="Contoh: alat mati saat dipakai di bed 3, adaptor sudah dicoba ulang.">{{ old('description') }}</textarea>
            </label>

            <div id="previewPanel" class="rounded-lg border border-cyan-100 bg-cyan-50 p-4">
                <div class="flex items-center gap-2 text-cyan-800">
                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                    <p class="text-sm font-bold">Preview rule engine</p>
                </div>
                <div id="previewContent" class="mt-3 text-sm leading-6 text-cyan-900">
                    Pilih gejala untuk melihat indikasi awal.
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-3 text-sm font-semibold text-white hover:bg-cyan-800">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    Kirim Laporan
                </button>
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i data-lucide="image-plus" class="h-4 w-4"></i>
                    Lampirkan Foto
                </button>
            </div>
        </form>
    </section>
</div>

<div id="scanSuccessModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4" role="dialog" aria-modal="true" aria-labelledby="scanSuccessTitle">
    <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 p-5">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                </span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Scan berhasil</p>
                    <h3 id="scanSuccessTitle" class="mt-1 text-xl font-bold text-slate-950">Alat ditemukan</h3>
                </div>
            </div>
            <button type="button" data-close-scan-modal class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50" aria-label="Tutup modal">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        <div class="p-5">
            <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-4">
                <p id="scanModalDeviceName" class="text-lg font-bold text-slate-950">-</p>
                <p id="scanModalDeviceMeta" class="mt-1 text-sm font-semibold text-slate-600">-</p>
            </div>

            <div id="scanModalDevicePhotoWrapper" class="mt-4 hidden">
                <img id="scanModalDevicePhoto" src="#" alt="Foto Alat" class="h-40 w-full object-cover rounded-lg border border-slate-200">
            </div>

            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Serial Number</dt>
                    <dd id="scanModalDeviceSerial" class="mt-1 font-semibold text-slate-950">-</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Model</dt>
                    <dd id="scanModalDeviceModel" class="mt-1 font-semibold text-slate-950">-</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Lokasi</dt>
                    <dd id="scanModalDeviceUnit" class="mt-1 font-semibold text-slate-950">-</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Barcode</dt>
                    <dd id="scanModalDeviceBarcode" class="mt-1 font-semibold text-slate-950">-</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Tanggal Pembelian</dt>
                    <dd id="scanModalDevicePurchased" class="mt-1 font-semibold text-slate-950">-</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3">
                    <dt class="text-slate-500">Terakhir Maintenance</dt>
                    <dd id="scanModalDeviceLastMaintenance" class="mt-1 font-semibold text-emerald-700">-</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3 sm:col-span-2">
                    <dt class="text-slate-500">Terakhir Kalibrasi</dt>
                    <dd id="scanModalDeviceLastCalibration" class="mt-1 font-semibold text-cyan-700">-</dd>
                </div>
            </dl>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 p-5 sm:flex-row sm:justify-end">
            <button type="button" id="rescanButton" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="scan-line" class="h-4 w-4"></i>
                Scan Ulang
            </button>
            <button type="button" data-close-scan-modal class="inline-flex items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                <i data-lucide="clipboard-check" class="h-4 w-4"></i>
                Pilih Gejala
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    const devices = @json($scannerDevices);
    const checkboxes = Array.from(document.querySelectorAll('input[name="symptoms[]"]'));
    const selectedCounter = document.getElementById('selectedCounter');
    const previewContent = document.getElementById('previewContent');
    const scanButton = document.getElementById('scanButton');
    const scannerReader = document.getElementById('scannerReader');
    const scannerPlaceholder = document.getElementById('scannerPlaceholder');
    const scanStatus = document.getElementById('scanStatus');
    const manualScanInput = document.getElementById('manualScanInput');
    const manualScanButton = document.getElementById('manualScanButton');
    const imageScanInput = document.getElementById('imageScanInput');
    const deviceIdInput = document.getElementById('deviceIdInput');
    const deviceName = document.getElementById('deviceName');
    const deviceMeta = document.getElementById('deviceMeta');
    const deviceStatus = document.getElementById('deviceStatus');
    const deviceSerial = document.getElementById('deviceSerial');
    const deviceModel = document.getElementById('deviceModel');
    const deviceUnit = document.getElementById('deviceUnit');
    const deviceBarcode = document.getElementById('deviceBarcode');
    const scanSuccessModal = document.getElementById('scanSuccessModal');
    const scanModalDeviceName = document.getElementById('scanModalDeviceName');
    const scanModalDeviceMeta = document.getElementById('scanModalDeviceMeta');
    const scanModalDeviceSerial = document.getElementById('scanModalDeviceSerial');
    const scanModalDeviceModel = document.getElementById('scanModalDeviceModel');
    const scanModalDeviceUnit = document.getElementById('scanModalDeviceUnit');
    const scanModalDeviceBarcode = document.getElementById('scanModalDeviceBarcode');
    const rescanButton = document.getElementById('rescanButton');
    let scanner = null;
    let scanning = false;
    let scanHandled = false;
    let canvasFallbackTimer = null;
    let nativeDetectorTimer = null;

    const rules = {
        'PWR': {
            title: 'Sistem Power',
            severity: 'High',
            suggestion: 'Cek sumber daya: kabel, adaptor, baterai, sekering, power board, tombol, dan konektor internal.'
        },
        'ALM': {
            title: 'Alarm Aktif',
            severity: 'Medium',
            suggestion: 'Lakukan tindakan sesuai jenis alarm, lalu cocokkan dengan panduan user manual.'
        },
        'PRF': {
            title: 'Performa Flow',
            severity: 'Medium',
            suggestion: 'Periksa gelembung udara, sumbatan selang, ukuran syringe, dan konektor.'
        },
        'SNS': {
            title: 'Kerusakan Sensor',
            severity: 'Medium',
            suggestion: 'Bersihkan sensor dan periksa konektor sensor tekanan, udara, atau posisi plunger.'
        },
        'MTR': {
            title: 'Motor / Driver / Mekanik',
            severity: 'High',
            suggestion: 'Periksa motor, lead screw, mekanisme penggerak, dan driver motor.'
        },
        'SFW': {
            title: 'Software / System',
            severity: 'Low',
            suggestion: 'Restart alat, cek kode error, lalu siapkan update atau instal ulang firmware.'
        }
    };

    function renderPreview() {
        const selected = checkboxes.filter((item) => item.checked);
        selectedCounter.textContent = `${selected.length} dipilih`;

        if (!selected.length) {
            previewContent.textContent = 'Pilih gejala untuk melihat indikasi awal.';
            return;
        }

        const grouped = {};
        selected.forEach((item) => {
            const prefix = item.dataset.code.split('-')[0];
            grouped[prefix] = rules[prefix];
        });

        previewContent.innerHTML = Object.values(grouped).map((rule) => `
            <div class="mb-3 rounded-md bg-white p-3 ring-1 ring-cyan-100">
                <div class="flex items-center justify-between gap-2">
                    <strong>${rule.title}</strong>
                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold">${rule.severity}</span>
                </div>
                <p class="mt-1">${rule.suggestion}</p>
            </div>
        `).join('');
    }

    checkboxes.forEach((item) => item.addEventListener('change', renderPreview));

    function setScanButton(icon, label) {
        scanButton.innerHTML = `<i data-lucide="${icon}" class="h-4 w-4"></i> ${label}`;
        lucide.createIcons();
    }

    function normalizeCode(value) {
        return String(value || '')
            .trim()
            .replace(/[\u200B-\u200D\uFEFF]/g, '')
            .replace(/\s+/g, '')
            .toUpperCase();
    }

    function scannedCandidates(scannedText) {
        const raw = String(scannedText || '').trim();
        const normalizedRaw = normalizeCode(raw);
        const candidates = new Set([normalizedRaw]);

        try {
            const url = new URL(raw);
            candidates.add(normalizeCode(url.pathname.split('/').filter(Boolean).pop()));
            ['code', 'barcode', 'barcode_code', 'qr', 'device', 'inventory', 'inventory_number', 'serial', 'serial_number'].forEach((key) => {
                if (url.searchParams.has(key)) {
                    candidates.add(normalizeCode(url.searchParams.get(key)));
                }
            });
        } catch (error) {
            raw.split(/[\s|,;:=/\\?&]+/).forEach((part) => candidates.add(normalizeCode(part)));
        }

        return Array.from(candidates).filter(Boolean);
    }

    function deviceCodes(device) {
        return (device.scan_codes || [device.barcode, device.inventory_number, device.serial_number])
            .map((code) => normalizeCode(code))
            .filter(Boolean);
    }

    function findDevice(scannedText) {
        const candidates = scannedCandidates(scannedText);

        return devices.find((item) => deviceCodes(item).some((deviceCode) => candidates.includes(deviceCode)))
            || devices.find((item) => deviceCodes(item).some((deviceCode) => candidates.some((code) => (
                deviceCode.length >= 5
                && code.length >= 5
                && (code.includes(deviceCode) || deviceCode.includes(code))
            ))));
    }

    function renderDevice(device) {
        deviceIdInput.value = device.id;
        deviceName.textContent = device.name;
        deviceMeta.textContent = `${device.inventory_number} - ${device.unit}`;
        deviceStatus.textContent = device.status;
        deviceSerial.textContent = device.serial_number;
        deviceModel.textContent = device.model || '-';
        deviceUnit.textContent = device.unit;
        deviceBarcode.textContent = device.barcode;

        // new fields
        document.getElementById('devicePurchased').textContent = device.purchased_formatted || '-';
        document.getElementById('deviceLastMaintenance').textContent = device.last_maintenance_formatted || '-';
        document.getElementById('deviceLastCalibration').textContent = device.last_calibration_formatted || '-';

        const photoWrapper = document.getElementById('devicePhotoWrapper');
        const photoImg = document.getElementById('devicePhoto');
        if (device.photo_url) {
            photoImg.src = device.photo_url;
            photoWrapper.classList.remove('hidden');
        } else {
            photoImg.src = '#';
            photoWrapper.classList.add('hidden');
        }
    }

    function openScanSuccessModal(device) {
        scanModalDeviceName.textContent = device.name;
        scanModalDeviceMeta.textContent = `${device.inventory_number} - ${device.unit}`;
        scanModalDeviceSerial.textContent = device.serial_number || '-';
        scanModalDeviceModel.textContent = device.model || '-';
        scanModalDeviceUnit.textContent = device.unit || '-';
        scanModalDeviceBarcode.textContent = device.barcode || '-';

        // new fields
        document.getElementById('scanModalDevicePurchased').textContent = device.purchased_formatted || '-';
        document.getElementById('scanModalDeviceLastMaintenance').textContent = device.last_maintenance_formatted || '-';
        document.getElementById('scanModalDeviceLastCalibration').textContent = device.last_calibration_formatted || '-';

        const modalPhotoWrapper = document.getElementById('scanModalDevicePhotoWrapper');
        const modalPhotoImg = document.getElementById('scanModalDevicePhoto');
        if (device.photo_url) {
            modalPhotoImg.src = device.photo_url;
            modalPhotoWrapper.classList.remove('hidden');
        } else {
            modalPhotoImg.src = '#';
            modalPhotoWrapper.classList.add('hidden');
        }

        scanSuccessModal.classList.remove('hidden');
        scanSuccessModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        lucide.createIcons();
    }

    function closeScanSuccessModal() {
        scanSuccessModal.classList.add('hidden');
        scanSuccessModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    async function stopScanner(resetButton = true) {
        if (!scanner) {
            return;
        }

        stopCanvasFallback();

        try {
            if (scanning) {
                await scanner.stop();
            }
        } catch (error) {
            console.warn('Scanner stop failed:', error);
        }

        try {
            scanner.clear();
        } catch (error) {
            console.warn('Scanner clear failed:', error);
        }

        scanning = false;
        scannerReader.classList.add('hidden');
        scannerPlaceholder.classList.remove('hidden');

        if (resetButton) {
            setScanButton('camera', 'Mulai Scan');
        }
    }

    function stopCanvasFallback() {
        if (canvasFallbackTimer) {
            window.clearInterval(canvasFallbackTimer);
            canvasFallbackTimer = null;
        }

        if (nativeDetectorTimer) {
            window.clearInterval(nativeDetectorTimer);
            nativeDetectorTimer = null;
        }
    }

    function decodeCanvasWithJsQr(canvas, context) {
        if (!window.jsQR) {
            return null;
        }

        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const rawResult = window.jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'attemptBoth',
        });

        if (rawResult?.data) {
            return rawResult.data;
        }

        const boosted = new ImageData(new Uint8ClampedArray(imageData.data), imageData.width, imageData.height);

        for (let index = 0; index < boosted.data.length; index += 4) {
            const gray = (boosted.data[index] * 0.299) + (boosted.data[index + 1] * 0.587) + (boosted.data[index + 2] * 0.114);
            const value = gray > 135 ? 255 : 0;

            boosted.data[index] = value;
            boosted.data[index + 1] = value;
            boosted.data[index + 2] = value;
        }

        const boostedResult = window.jsQR(boosted.data, boosted.width, boosted.height, {
            inversionAttempts: 'attemptBoth',
        });

        return boostedResult?.data || null;
    }

    function startCanvasFallback() {
        if (!window.jsQR || canvasFallbackTimer) {
            return;
        }

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', { willReadFrequently: true });

        canvasFallbackTimer = window.setInterval(() => {
            if (!scanning || scanHandled) {
                stopCanvasFallback();
                return;
            }

            const video = scannerReader.querySelector('video');

            if (!video || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA || !video.videoWidth || !video.videoHeight) {
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            let result = decodeCanvasWithJsQr(canvas, context);

            if (!result) {
                const cropSize = Math.floor(Math.min(video.videoWidth, video.videoHeight) * 0.82);
                const cropX = Math.floor((video.videoWidth - cropSize) / 2);
                const cropY = Math.floor((video.videoHeight - cropSize) / 2);

                canvas.width = 900;
                canvas.height = 900;
                context.imageSmoothingEnabled = false;
                context.drawImage(video, cropX, cropY, cropSize, cropSize, 0, 0, canvas.width, canvas.height);
                result = decodeCanvasWithJsQr(canvas, context);
            }

            if (result) {
                handleScanSuccess(result);
            }
        }, 350);
    }

    function startNativeDetectorFallback() {
        if (!('BarcodeDetector' in window) || nativeDetectorTimer) {
            return;
        }

        let detector = null;

        try {
            detector = new BarcodeDetector({
                formats: ['qr_code', 'code_128', 'code_39', 'ean_13', 'ean_8', 'upc_a', 'upc_e'],
            });
        } catch (error) {
            try {
                detector = new BarcodeDetector();
            } catch (fallbackError) {
                console.warn('Native barcode detector unavailable:', fallbackError);
                return;
            }
        }

        nativeDetectorTimer = window.setInterval(async () => {
            if (!scanning || scanHandled) {
                stopCanvasFallback();
                return;
            }

            const video = scannerReader.querySelector('video');

            if (!video || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) {
                return;
            }

            try {
                const results = await detector.detect(video);
                const decoded = results.find((item) => item.rawValue)?.rawValue;

                if (decoded) {
                    await handleScanSuccess(decoded);
                }
            } catch (error) {
                console.warn('Native barcode detect failed:', error);
            }
        }, 300);
    }

    async function handleScanSuccess(decodedText) {
        if (scanHandled && scanning) {
            return;
        }

        scanHandled = true;
        const device = findDevice(decodedText);

        if (!device) {
            scanStatus.textContent = `QR "${decodedText}" tidak ditemukan di data alat.`;
            await stopScanner();
            scanHandled = false;
            return;
        }

        renderDevice(device);
        scanStatus.textContent = `Alat terbaca: ${device.barcode}`;
        await stopScanner(false);
        setScanButton('check-circle', 'Alat Terbaca');
        openScanSuccessModal(device);
    }

    function scannerFormats() {
        if (!window.Html5QrcodeSupportedFormats) {
            return undefined;
        }

        const formats = window.Html5QrcodeSupportedFormats;

        return [
            formats.QR_CODE,
            formats.CODE_128,
            formats.CODE_39,
            formats.EAN_13,
            formats.EAN_8,
            formats.UPC_A,
            formats.UPC_E,
        ].filter((format) => format !== undefined && format !== null);
    }

    function scannerConfig() {
        const formats = scannerFormats();

        return formats
            ? { formatsToSupport: formats, verbose: false }
            : { verbose: false };
    }

    function ensureScanner() {
        scanner = scanner || new Html5Qrcode('scannerReader', scannerConfig());

        return scanner;
    }

    function qrbox(viewfinderWidth, viewfinderHeight) {
        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
        const size = Math.floor(Math.min(Math.max(minEdge * 0.9, 260), 430));

        return { width: size, height: size };
    }

    async function applyCameraEnhancements() {
        const video = scannerReader.querySelector('video');
        const track = video?.srcObject?.getVideoTracks?.()[0];

        if (!track?.getCapabilities || !track?.applyConstraints) {
            return;
        }

        const capabilities = track.getCapabilities();
        const advanced = [];

        if (capabilities.focusMode?.includes('continuous')) {
            advanced.push({ focusMode: 'continuous' });
        }

        if (capabilities.exposureMode?.includes('continuous')) {
            advanced.push({ exposureMode: 'continuous' });
        }

        if (capabilities.whiteBalanceMode?.includes('continuous')) {
            advanced.push({ whiteBalanceMode: 'continuous' });
        }

        if (!advanced.length) {
            return;
        }

        try {
            await track.applyConstraints({ advanced });
        } catch (error) {
            console.warn('Camera enhancements unavailable:', error);
        }
    }

    async function startScanner() {
        if (!window.Html5Qrcode) {
            scanStatus.textContent = 'Library scanner belum termuat. Pastikan browser terhubung internet.';
            return;
        }

        scanHandled = false;
        scannerPlaceholder.classList.add('hidden');
        scannerReader.classList.remove('hidden');
        scanStatus.textContent = 'Meminta akses kamera...';
        setScanButton('x', 'Stop Scan');

        try {
            let preferredId = null;
            try {
                const cameras = await Html5Qrcode.getCameras();
                const preferred = cameras.find((camera) => /back|rear|environment|belakang/i.test(camera.label))
                    || cameras[cameras.length - 1];
                preferredId = preferred?.id || null;
            } catch (error) {
                console.warn('Camera list unavailable:', error);
            }

            const baseScanConfig = {
                fps: 15,
                qrbox,
                disableFlip: false,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true,
                },
            };

            const highResolution = {
                width: { ideal: 1920 },
                height: { ideal: 1080 },
            };

            const trials = [];

            // 1. Preferred camera ID + high resolution (1080p) + 4:3 aspect ratio
            if (preferredId) {
                trials.push({
                    cameraConfig: { deviceId: { exact: preferredId }, ...highResolution },
                    scanConfig: { ...baseScanConfig, aspectRatio: 1.333334 }
                });
                // 2. Preferred camera ID + high resolution (1080p) + no aspect ratio constraint
                trials.push({
                    cameraConfig: { deviceId: { exact: preferredId }, ...highResolution },
                    scanConfig: baseScanConfig
                });
                // 3. Preferred camera ID string (default resolution & aspect ratio)
                trials.push({
                    cameraConfig: preferredId,
                    scanConfig: baseScanConfig
                });
            }

            // 4. facingMode environment + high resolution + 4:3 aspect ratio
            trials.push({
                cameraConfig: { facingMode: { exact: 'environment' }, ...highResolution },
                scanConfig: { ...baseScanConfig, aspectRatio: 1.333334 }
            });
            trials.push({
                cameraConfig: { facingMode: 'environment', ...highResolution },
                scanConfig: { ...baseScanConfig, aspectRatio: 1.333334 }
            });

            // 5. facingMode environment + high resolution + no aspect ratio constraint
            trials.push({
                cameraConfig: { facingMode: { exact: 'environment' }, ...highResolution },
                scanConfig: baseScanConfig
            });
            trials.push({
                cameraConfig: { facingMode: 'environment', ...highResolution },
                scanConfig: baseScanConfig
            });

            // 6. facingMode environment + default resolution (no aspect ratio constraint)
            trials.push({
                cameraConfig: { facingMode: 'environment' },
                scanConfig: baseScanConfig
            });

            // 7. High resolution default camera (no aspect ratio constraint)
            trials.push({
                cameraConfig: highResolution,
                scanConfig: baseScanConfig
            });

            // 8. Default camera (no constraints)
            trials.push({
                cameraConfig: {},
                scanConfig: baseScanConfig
            });

            let lastError = null;

            for (const trial of trials) {
                try {
                    ensureScanner();
                    scanning = true;
                    await scanner.start(trial.cameraConfig, trial.scanConfig, handleScanSuccess, () => {});
                    lastError = null;
                    break;
                } catch (error) {
                    console.warn('Scanner start failed for trial config:', trial, error);
                    lastError = error;
                    scanning = false;

                    try {
                        if (scanner) {
                            await scanner.clear();
                        }
                    } catch (clearError) {
                        console.warn('Scanner clear failed during loop:', clearError);
                    }
                    scanner = null; // force recreation of scanner on next iteration
                }
            }

            if (lastError) {
                throw lastError;
            }

            await applyCameraEnhancements();
            scanStatus.textContent = 'Kamera aktif. Jauhkan sedikit sampai QR tajam, lalu tahan 1-2 detik.';
            startCanvasFallback();
            startNativeDetectorFallback();
        } catch (error) {
            console.error('All scanner start attempts failed:', error);
            stopCanvasFallback();
            scanning = false;
            scannerReader.classList.add('hidden');
            scannerPlaceholder.classList.remove('hidden');
            scanStatus.textContent = 'Kamera tidak bisa dibuka. Izinkan akses kamera di browser, lalu coba lagi.';
            setScanButton('camera', 'Mulai Scan');
        }
    }

    function loadImage(file) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            const objectUrl = URL.createObjectURL(file);

            image.onload = () => {
                URL.revokeObjectURL(objectUrl);
                resolve(image);
            };
            image.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                reject(new Error('Gambar tidak bisa dibaca.'));
            };
            image.src = objectUrl;
        });
    }

    async function decodeImageWithJsQr(file) {
        if (!window.jsQR) {
            return null;
        }

        const image = await loadImage(file);
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', { willReadFrequently: true });
        const maxSize = 1800;
        const scale = Math.min(1, maxSize / Math.max(image.naturalWidth, image.naturalHeight));

        canvas.width = Math.max(1, Math.floor(image.naturalWidth * scale));
        canvas.height = Math.max(1, Math.floor(image.naturalHeight * scale));
        context.drawImage(image, 0, 0, canvas.width, canvas.height);

        let result = decodeCanvasWithJsQr(canvas, context);

        if (!result) {
            const cropSize = Math.floor(Math.min(canvas.width, canvas.height) * 0.86);
            const cropX = Math.floor((canvas.width - cropSize) / 2);
            const cropY = Math.floor((canvas.height - cropSize) / 2);
            const crop = document.createElement('canvas');
            const cropContext = crop.getContext('2d', { willReadFrequently: true });

            crop.width = 1200;
            crop.height = 1200;
            cropContext.imageSmoothingEnabled = false;
            cropContext.drawImage(canvas, cropX, cropY, cropSize, cropSize, 0, 0, crop.width, crop.height);
            result = decodeCanvasWithJsQr(crop, cropContext);
        }

        return result;
    }

    async function scanImageFile(file) {
        if (!file) {
            return;
        }

        if (scanning) {
            await stopScanner();
        }

        scanHandled = false;
        scanStatus.textContent = 'Membaca QR dari foto...';

        try {
            ensureScanner();

            if (typeof scanner.scanFile === 'function') {
                const decodedText = await scanner.scanFile(file, false);
                await handleScanSuccess(decodedText);
                return;
            }
        } catch (error) {
            console.warn('Html5 scanFile failed:', error);
        }

        try {
            const decodedText = await decodeImageWithJsQr(file);

            if (decodedText) {
                await handleScanSuccess(decodedText);
                return;
            }
        } catch (error) {
            console.warn('jsQR image decode failed:', error);
        }

        scanStatus.textContent = 'QR di foto belum terbaca. Pakai foto yang lebih tajam atau masukkan kode label manual.';
    }

    scanButton.addEventListener('click', async () => {
        if (scanning) {
            await stopScanner();
            return;
        }

        await startScanner();
    });

    manualScanButton.addEventListener('click', async () => {
        const code = manualScanInput.value.trim();

        if (!code) {
            scanStatus.textContent = 'Masukkan kode label alat terlebih dahulu.';
            return;
        }

        await handleScanSuccess(code);
    });

    manualScanInput.addEventListener('keydown', async (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            await handleScanSuccess(manualScanInput.value);
        }
    });

    imageScanInput.addEventListener('change', async () => {
        await scanImageFile(imageScanInput.files?.[0]);
        imageScanInput.value = '';
    });

    document.querySelectorAll('[data-close-scan-modal]').forEach((button) => {
        button.addEventListener('click', closeScanSuccessModal);
    });

    scanSuccessModal.addEventListener('click', (event) => {
        if (event.target === scanSuccessModal) {
            closeScanSuccessModal();
        }
    });

    rescanButton.addEventListener('click', async () => {
        closeScanSuccessModal();
        await startScanner();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !scanSuccessModal.classList.contains('hidden')) {
            closeScanSuccessModal();
        }
    });
</script>
@endpush

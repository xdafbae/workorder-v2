<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun | WO Infusion Pump</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="grid min-h-screen lg:grid-cols-[1.1fr_.9fr]">
        <section class="flex items-center px-5 py-10 sm:px-10 lg:px-16">
            <div class="w-full max-w-xl mx-auto lg:mx-0">
                <div class="mb-8 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-cyan-700 text-white">
                        <i data-lucide="activity" class="h-6 w-6"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">WO Medika</p>
                        <h1 class="text-xl font-bold text-slate-950">Infusion Pump</h1>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-bold text-slate-950">Daftar Akun Baru</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Silakan daftarkan diri Anda sebagai staf medis perawat, teknisi elektromedis, atau admin sistem.</p>

                    @if ($errors->any())
                        <div class="mt-5 rounded-md border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                        @csrf
                        
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Nama Lengkap</span>
                            <input name="name" type="text" required value="{{ old('name') }}" placeholder="Contoh: Ns. Rina Marlina" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Alamat Email</span>
                            <input name="email" type="email" required value="{{ old('email') }}" placeholder="Contoh: staf@rs.test" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        </label>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Peran / Role</span>
                                <select id="roleSelector" name="role" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                                    <option value="nurse" @selected(old('role', 'nurse') === 'nurse')>Perawat (Nurse)</option>
                                    <option value="technician" @selected(old('role') === 'technician')>Teknisi Elektromedis</option>
                                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                </select>
                            </label>

                            <!-- Dynamic Unit Selector (Shown only when nurse is selected) -->
                            <div id="unitWrapper" class="block">
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700">Unit / Ruangan</span>
                                    <select id="unitInput" name="unit_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                                        <option value="">-- Pilih Ruangan --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" @selected((string) old('unit_id') === (string) $unit->id)>{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Password</span>
                                <input name="password" type="password" required placeholder="Minimal 6 karakter" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Konfirmasi Password</span>
                                <input name="password_confirmation" type="password" required placeholder="Ulangi password" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                            </label>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                            <i data-lucide="user-plus" class="h-4 w-4"></i>
                            Daftar Sekarang
                        </button>
                    </form>

                    <div class="mt-4 text-center">
                        <p class="text-sm text-slate-500 font-medium">Sudah memiliki akun? <a href="{{ route('login') }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Masuk di sini</a></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Right Side Hero (operational dashboard snippet matching login page) -->
        <section class="hidden border-l border-slate-200 bg-white p-8 lg:flex lg:items-center">
            <div class="w-full rounded-lg border border-slate-200 bg-slate-50 p-6">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Status Operasional</p>
                        <h3 class="text-2xl font-bold text-slate-950">Infusion Pump WO</h3>
                    </div>
                    <span class="rounded-md bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                </div>
                <p class="text-sm text-slate-600 leading-6">Sistem digitalisasi pelaporan kerusakan alat kesehatan (Work Order) yang terintegrasi barcode scanner, penanganan otomatis berbasis Rule-Based Engine, dan tracking real-time status pemeliharaan.</p>
                <div class="mt-5 space-y-3">
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded bg-cyan-50 text-cyan-700">
                            <i data-lucide="scan-qr-code" class="h-5 w-5"></i>
                        </div>
                        <p class="text-sm font-medium text-slate-700">Scan QR Code Alat Kesehatan</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded bg-amber-50 text-amber-700">
                            <i data-lucide="calculator" class="h-5 w-5"></i>
                        </div>
                        <p class="text-sm font-medium text-slate-700">Deteksi Gejala Otomatis (Rules)</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        const roleSelector = document.getElementById('roleSelector');
        const unitWrapper = document.getElementById('unitWrapper');
        const unitInput = document.getElementById('unitInput');

        const toggleUnitField = () => {
            const role = roleSelector.value;
            if (role === 'nurse') {
                unitWrapper.classList.remove('hidden');
                unitInput.required = true;
            } else {
                unitWrapper.classList.add('hidden');
                unitInput.required = false;
                unitInput.value = '';
            }
        };

        roleSelector.addEventListener('change', toggleUnitField);

        // Run once on load to restore state (e.g. on validation redirect)
        toggleUnitField();

        lucide.createIcons();
    </script>
</body>
</html>

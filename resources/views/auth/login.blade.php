<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | WO Infusion Pump</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="grid min-h-screen lg:grid-cols-[1.1fr_.9fr]">
        <section class="flex items-center px-5 py-10 sm:px-10 lg:px-16">
            <div class="w-full max-w-xl">
                <div class="mb-10 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-cyan-700 text-white">
                        <i data-lucide="activity" class="h-6 w-6"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">WO Medika</p>
                        <h1 class="text-xl font-bold text-slate-950">Infusion Pump</h1>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-2xl font-bold text-slate-950">Masuk ke sistem</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Masuk dengan akun demo atau kredensial yang sudah disiapkan di seeder backend.</p>

                    @if ($errors->any())
                        <div class="mt-5 rounded-md border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Email</span>
                            <input name="email" type="email" value="{{ old('email', 'perawat@rs.test') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Password</span>
                            <input name="password" type="password" value="password" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        </label>
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                            <i data-lucide="log-in" class="h-4 w-4"></i>
                            Masuk
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <p class="text-sm text-slate-500 font-medium">Belum memiliki akun? <a href="{{ route('register') }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Daftar di sini</a></p>
                    </div>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <form method="POST" action="{{ route('demo.login', 'nurse') }}">
                        @csrf
                        <button class="w-full rounded-lg border border-slate-200 bg-white p-4 text-left text-sm font-semibold text-slate-700 hover:border-cyan-300 hover:text-cyan-700">
                        <i data-lucide="user-round" class="mb-3 h-5 w-5"></i>
                        Perawat
                        </button>
                    </form>
                    <form method="POST" action="{{ route('demo.login', 'technician') }}">
                        @csrf
                        <button class="w-full rounded-lg border border-slate-200 bg-white p-4 text-left text-sm font-semibold text-slate-700 hover:border-cyan-300 hover:text-cyan-700">
                        <i data-lucide="wrench" class="mb-3 h-5 w-5"></i>
                        Teknisi
                        </button>
                    </form>
                    <form method="POST" action="{{ route('demo.login', 'admin') }}">
                        @csrf
                        <button class="w-full rounded-lg border border-slate-200 bg-white p-4 text-left text-sm font-semibold text-slate-700 hover:border-cyan-300 hover:text-cyan-700">
                        <i data-lucide="shield-check" class="mb-3 h-5 w-5"></i>
                        Admin
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="hidden border-l border-slate-200 bg-white p-8 lg:flex lg:items-center">
            <div class="w-full rounded-lg border border-slate-200 bg-slate-50 p-6">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Status Operasional</p>
                        <h3 class="text-2xl font-bold text-slate-950">12 WO aktif</h3>
                    </div>
                    <span class="rounded-md bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Online</span>
                </div>
                <div class="space-y-3">
                    <div class="rounded-lg border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-950">WO-2026-0008</p>
                            <span class="text-xs font-semibold text-amber-700">Menunggu</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Syringe Pump Terumo TE-331, ICU Lantai 2</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-950">Indikasi Awal</p>
                            <span class="text-xs font-semibold text-red-700">High</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Sistem Power, baterai, adaptor, fuse, dan konektor internal.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>

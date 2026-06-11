@php
    $title = $title ?? 'Work Order Infusion Pump';
    $role = $role ?? 'Prototype';
    $active = $active ?? 'dashboard';
    $navItems = [
        ['key' => 'dashboard', 'label' => 'Perawat', 'href' => '/dashboard/perawat', 'icon' => 'layout-dashboard'],
        ['key' => 'report', 'label' => 'Lapor WO', 'href' => '/work-orders/create', 'icon' => 'scan-line'],
        ['key' => 'technician', 'label' => 'Teknisi', 'href' => '/dashboard/teknisi', 'icon' => 'wrench'],
        ['key' => 'admin', 'label' => 'Admin', 'href' => '/dashboard/admin', 'icon' => 'bar-chart-3'],
        ['key' => 'devices', 'label' => 'Alat', 'href' => '/devices', 'icon' => 'hard-drive'],
        ['key' => 'rules', 'label' => 'Rules', 'href' => '/admin/rules', 'icon' => 'git-branch'],
        ['key' => 'reports', 'label' => 'Laporan', 'href' => '/reports', 'icon' => 'file-text'],
    ];
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | WO Infusion Pump</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        hospital: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            500: '#0891b2',
                            600: '#0e7490',
                            700: '#155e75'
                        }
                    },
                    boxShadow: {
                        panel: '0 1px 2px rgba(15, 23, 42, 0.06), 0 12px 30px rgba(15, 23, 42, 0.05)'
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .scan-frame {
            background:
                linear-gradient(90deg, rgba(8,145,178,.38) 1px, transparent 1px),
                linear-gradient(0deg, rgba(8,145,178,.38) 1px, transparent 1px),
                #0f172a;
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen lg:flex">
        <aside class="border-b border-slate-200 bg-white lg:sticky lg:top-0 lg:h-screen lg:w-72 lg:border-b-0 lg:border-r">
            <div class="flex items-center gap-3 px-5 py-5">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-hospital-600 text-white">
                    <i data-lucide="activity" class="h-5 w-5"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-hospital-700">WO Medika</p>
                    <h1 class="text-base font-bold text-slate-950">Infusion Pump</h1>
                </div>
            </div>

            <nav class="flex gap-2 overflow-x-auto px-4 pb-4 lg:block lg:space-y-1 lg:overflow-visible">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}"
                        class="flex min-w-max items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition {{ $active === $item['key'] ? 'bg-hospital-50 text-hospital-700 ring-1 ring-hospital-100' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="hidden px-5 py-5 lg:block">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Mode</p>
                    <p class="mt-1 text-sm font-semibold text-slate-950">Backend aktif</p>
                    <p class="mt-2 text-xs leading-5 text-slate-500">Data berasal dari migration, seeder, controller, dan rule engine Laravel.</p>
                </div>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ $role }}</p>
                        <h2 class="text-xl font-bold text-slate-950 sm:text-2xl">{{ $title }}</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="/work-orders/create" class="inline-flex items-center gap-2 rounded-md bg-hospital-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-hospital-700">
                            <i data-lucide="scan-line" class="h-4 w-4"></i>
                            <span>WO Baru</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-600 hover:bg-slate-100" aria-label="Keluar">
                                <i data-lucide="log-out" class="h-4 w-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('warning'))
                    <div class="mb-5 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm font-semibold text-amber-700">
                        {{ session('warning') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-5 rounded-md border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>

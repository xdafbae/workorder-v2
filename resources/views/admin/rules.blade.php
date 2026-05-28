@extends('layouts.app')

@section('content')
<div>
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Rule-based engine</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Pemetaan gejala ke indikasi</h3>
            </div>
            <button type="button" data-open-rule-modal class="inline-flex items-center gap-2 rounded-md bg-cyan-700 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Tambah Rule
            </button>
        </div>

        <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Rule</th>
                            <th class="px-4 py-3">Gejala</th>
                            <th class="px-4 py-3">Indikasi</th>
                            <th class="px-4 py-3">Bobot</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($rules as $rule)
                            <tr>
                                <td class="px-4 py-4 font-semibold text-slate-950">{{ $rule['name'] }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $rule['symptoms'] }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $rule['indication'] }}</td>
                                <td class="px-4 py-4">
                                    <span class="rounded-md bg-cyan-50 px-2 py-1 text-xs font-semibold text-cyan-700">{{ $rule['weight'] }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $rule['active'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $rule['active'] ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <button class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50" aria-label="Edit rule">
                                        <i data-lucide="settings-2" class="h-4 w-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div id="ruleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Tambah rule</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Draft konfigurasi</h3>
            </div>
            <button type="button" data-close-rule-modal class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50" aria-label="Tutup modal">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('rules.store') }}" class="space-y-4 px-5 py-5">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-slate-700">Nama rule</span>
                    <input name="name" type="text" required value="{{ old('name', 'Power failure basic') }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Gejala</span>
                    <select name="symptoms[]" multiple required class="mt-1 h-36 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        @foreach ($symptoms as $symptom)
                            <option value="{{ $symptom->id }}" @selected(in_array((string) $symptom->id, old('symptoms', []), true))>{{ $symptom->code }} - {{ $symptom->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Indikasi</span>
                    <select name="indications[]" multiple required class="mt-1 h-36 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                        @foreach ($indicationsList as $indication)
                            <option value="{{ $indication->id }}" @selected(in_array((string) $indication->id, old('indications', []), true))>{{ $indication->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-slate-700">Bobot prioritas</span>
                    <input name="weight" type="range" min="1" max="100" value="{{ old('weight', 95) }}" class="mt-2 w-full accent-cyan-700">
                </label>

                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                    <input name="is_active" value="1" type="checkbox" @checked(old('is_active', '1') === '1') class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                    Aktif
                </label>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4">
                <button type="button" data-close-rule-modal class="inline-flex items-center justify-center rounded-md border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Simpan Rule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const ruleModal = document.getElementById('ruleModal');

    const openRuleModal = () => {
        ruleModal.classList.remove('hidden');
        ruleModal.classList.add('flex');
    };

    const closeRuleModal = () => {
        ruleModal.classList.add('hidden');
        ruleModal.classList.remove('flex');
    };

    document.querySelectorAll('[data-open-rule-modal]').forEach((button) => {
        button.addEventListener('click', openRuleModal);
    });

    document.querySelectorAll('[data-close-rule-modal]').forEach((button) => {
        button.addEventListener('click', closeRuleModal);
    });

    ruleModal.addEventListener('click', (event) => {
        if (event.target === ruleModal) {
            closeRuleModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !ruleModal.classList.contains('hidden')) {
            closeRuleModal();
        }
    });

    @if ($errors->any())
        openRuleModal();
    @endif
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-slate-500 font-medium">Total Pengguna</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['total'] }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                    <i data-lucide="users" class="h-6 w-6"></i>
                </div>
            </div>
            <p class="mt-2.5 text-xs text-slate-500 font-medium">Staf terdaftar aktif</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700 font-medium">Perawat (Nurse)</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['nurse'] }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                    <i data-lucide="heart" class="h-6 w-6"></i>
                </div>
            </div>
            <p class="mt-2.5 text-xs text-slate-500 font-medium">Pelapor kerusakan alat</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-amber-700 font-medium">Teknisi</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['technician'] }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i data-lucide="wrench" class="h-6 w-6"></i>
                </div>
            </div>
            <p class="mt-2.5 text-xs text-slate-500 font-medium">Pemeriksa & perbaikan alat</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-purple-700 font-medium">Admin & Super</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['admin'] }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                    <i data-lucide="shield-check" class="h-6 w-6"></i>
                </div>
            </div>
            <p class="mt-2.5 text-xs text-slate-500 font-medium">Supervisor & IT Admin</p>
        </div>
    </div>

    <!-- Main Content Panel -->
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Daftar Pengguna</p>
                <h3 class="mt-1 text-xl font-bold text-slate-950">Kelola Akun Perawat, Teknisi, & Admin</h3>
            </div>
            <div class="flex gap-2">
                <button type="button" data-open-user-modal class="inline-flex items-center gap-2 rounded-md bg-cyan-700 px-3.5 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Tambah Pengguna
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('users.index') }}" class="mt-5 grid gap-3 sm:grid-cols-[1fr_200px_auto]">
            <label class="relative block">
                <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input name="search" value="{{ $filters['search'] }}" type="search" placeholder="Cari nama, email, atau ruangan..." class="w-full rounded-md border border-slate-300 py-2.5 pl-9 pr-3 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
            </label>
            <select name="role" class="rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                <option value="">Semua Role</option>
                <option value="nurse" @selected($filters['role'] === 'nurse')>Perawat (Nurse)</option>
                <option value="technician" @selected($filters['role'] === 'technician')>Teknisi</option>
                <option value="admin" @selected($filters['role'] === 'admin')>Admin</option>
                <option value="super_admin" @selected($filters['role'] === 'super_admin')>Super Admin</option>
            </select>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="filter" class="h-4 w-4"></i>
                Filter
            </button>
        </form>

        <!-- Table -->
        <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Nama & Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Ruangan/Unit</th>
                            <th class="px-4 py-3">Tgl Registrasi</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($users as $item)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-slate-950">{{ $item->name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $item->email }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($item->role === 'nurse')
                                        <span class="inline-flex items-center gap-1 rounded-md bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-700">
                                            <i data-lucide="heart" class="h-3 w-3"></i> Perawat
                                        </span>
                                    @elseif ($item->role === 'technician')
                                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            <i data-lucide="wrench" class="h-3 w-3"></i> Teknisi
                                        </span>
                                    @elseif ($item->role === 'admin')
                                        <span class="inline-flex items-center gap-1 rounded-md bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700">
                                            <i data-lucide="shield" class="h-3 w-3"></i> Admin
                                        </span>
                                    @elseif ($item->role === 'super_admin')
                                        <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                            <i data-lucide="shield-alert" class="h-3 w-3"></i> Super Admin
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ $item->unit?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-4 text-slate-500 text-xs">
                                    {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '—' }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            data-edit-user
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}"
                                            data-email="{{ $item->email }}"
                                            data-role="{{ $item->role }}"
                                            data-unit-id="{{ $item->unit_id }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50" title="Edit Pengguna">
                                            <i data-lucide="pencil" class="h-4 w-4"></i>
                                        </button>
                                        <form method="POST" action="{{ route('users.destroy', $item->id) }}" data-delete-user>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-red-100 text-red-600 hover:bg-red-50" title="Hapus Pengguna" @disabled($item->id === auth()->id())>
                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm font-medium text-slate-500">Data pengguna tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- Modal Form -->
<div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-lg bg-white shadow-xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <div>
                <p id="userModalEyebrow" class="text-sm font-semibold uppercase tracking-wide text-cyan-700">Tambah pengguna</p>
                <h3 id="userModalTitle" class="mt-1 text-xl font-bold text-slate-950">Data pengguna baru</h3>
            </div>
            <button type="button" data-close-user-modal class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50" aria-label="Tutup modal">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        <form id="userForm" method="POST" action="{{ route('users.store') }}" class="space-y-4 px-5 py-5">
            @csrf
            <input id="userFormMethod" type="hidden" name="_method" value="POST" disabled>
            <input id="userIdInput" type="hidden" name="id" value="{{ old('id') }}">

            <div class="space-y-4">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Nama Lengkap</span>
                    <input id="userNameInput" name="name" required value="{{ old('name') }}" placeholder="Nama Lengkap" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Alamat Email</span>
                    <input id="userEmailInput" name="email" type="email" required value="{{ old('email') }}" placeholder="Contoh: perawat@rs.test" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Role / Hak Akses</span>
                        <select id="userRoleInput" name="role" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                            <option value="nurse" @selected(old('role') === 'nurse')>Perawat (Nurse)</option>
                            <option value="technician" @selected(old('role') === 'technician')>Teknisi</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            <option value="super_admin" @selected(old('role') === 'super_admin')>Super Admin</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Password</span>
                        <input id="userPasswordInput" name="password" type="password" required placeholder="Minimal 6 karakter" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                    </label>
                </div>

                <!-- Unit selection container (Dynamic visibility) -->
                <div id="unitSelectionWrapper" class="block">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Unit / Ruangan Kerja</span>
                        <select id="userUnitInput" name="unit_id" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @selected((string) old('unit_id') === (string) $unit->id)>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4">
                <button type="button" data-close-user-modal class="inline-flex items-center justify-center rounded-md border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button id="userSubmitButton" type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const userModal = document.getElementById('userModal');
    const userForm = document.getElementById('userForm');
    const userFormMethod = document.getElementById('userFormMethod');
    const userIdInput = document.getElementById('userIdInput');
    const userModalEyebrow = document.getElementById('userModalEyebrow');
    const userModalTitle = document.getElementById('userModalTitle');
    const userNameInput = document.getElementById('userNameInput');
    const userEmailInput = document.getElementById('userEmailInput');
    const userRoleInput = document.getElementById('userRoleInput');
    const userPasswordInput = document.getElementById('userPasswordInput');
    const userUnitInput = document.getElementById('userUnitInput');
    const unitSelectionWrapper = document.getElementById('unitSelectionWrapper');
    const userSubmitButton = document.getElementById('userSubmitButton');
    
    const storeUserUrl = @json(route('users.store'));
    const updateUserUrl = @json(route('users.update', ['user' => '__ID__']));

    const openUserModal = () => {
        userModal.classList.remove('hidden');
        userModal.classList.add('flex');
    };
    const closeUserModal = () => {
        userModal.classList.add('hidden');
        userModal.classList.remove('flex');
    };

    // Toggle unit select input visibility based on role selection
    const toggleUnitVisibility = () => {
        const role = userRoleInput.value;
        if (role === 'nurse') {
            unitSelectionWrapper.classList.remove('hidden');
            userUnitInput.required = true;
        } else {
            unitSelectionWrapper.classList.add('hidden');
            userUnitInput.required = false;
            userUnitInput.value = '';
        }
    };

    userRoleInput.addEventListener('change', toggleUnitVisibility);

    const setFormMode = (mode, user = {}) => {
        const isEdit = mode === 'edit';

        userForm.action = isEdit ? updateUserUrl.replace('__ID__', user.id) : storeUserUrl;
        userFormMethod.disabled = !isEdit;
        userFormMethod.value = isEdit ? 'PATCH' : 'POST';
        userIdInput.value = user.id || '';
        userModalEyebrow.textContent = isEdit ? 'Edit pengguna' : 'Tambah pengguna';
        userModalTitle.textContent = isEdit ? user.name : 'Data pengguna baru';
        userSubmitButton.innerHTML = `<i data-lucide="save" class="h-4 w-4"></i> ${isEdit ? 'Update Pengguna' : 'Simpan Pengguna'}`;
        
        userNameInput.value = user.name || '';
        userEmailInput.value = user.email || '';
        userRoleInput.value = user.role || 'nurse';
        
        // When editing, password is not required
        if (isEdit) {
            userPasswordInput.required = false;
            userPasswordInput.placeholder = 'Kosongkan jika tidak ingin mengubah password';
        } else {
            userPasswordInput.required = true;
            userPasswordInput.placeholder = 'Minimal 6 karakter';
        }
        userPasswordInput.value = '';

        userUnitInput.value = user.unitId || '';

        // Trigger view logic for unit selector
        toggleUnitVisibility();
        lucide.createIcons();
    };

    document.querySelectorAll('[data-open-user-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            setFormMode('create');
            openUserModal();
        });
    });

    document.querySelectorAll('[data-edit-user]').forEach((button) => {
        button.addEventListener('click', () => {
            setFormMode('edit', button.dataset);
            openUserModal();
        });
    });

    document.querySelectorAll('[data-delete-user]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!confirm('Hapus pengguna ini secara permanen dari sistem?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-close-user-modal]').forEach((button) => {
        button.addEventListener('click', closeUserModal);
    });

    userModal.addEventListener('click', (event) => {
        if (event.target === userModal) {
            closeUserModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !userModal.classList.contains('hidden')) {
            closeUserModal();
        }
    });

    // Check if validation errors occurred on form load
    @if ($errors->any())
        // Re-open modal on error
        const oldId = @json(old('id'));
        if (oldId) {
            // Find edit button matching the ID to retrieve user dataset, or manually construct it
            const editBtn = document.querySelector(`[data-edit-user][data-id="${oldId}"]`);
            if (editBtn) {
                setFormMode('edit', editBtn.dataset);
            } else {
                setFormMode('create');
            }
        } else {
            setFormMode('create');
        }
        openUserModal();
    @endif
</script>
@endsection

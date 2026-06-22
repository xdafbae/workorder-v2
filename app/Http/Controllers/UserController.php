<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');

        $users = User::query()
            ->with('unit')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('unit', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($roleFilter, ['nurse', 'technician', 'admin', 'super_admin'], true), function ($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => User::count(),
            'nurse' => User::where('role', 'nurse')->count(),
            'technician' => User::where('role', 'technician')->count(),
            'admin' => User::whereIn('role', ['admin', 'super_admin'])->count(),
        ];

        return view('admin.users', [
            'title' => 'Manajemen Pengguna',
            'role' => 'Supervisor / Admin',
            'active' => 'users',
            'users' => $users,
            'units' => Unit::query()->orderBy('name')->get(),
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'role' => $roleFilter,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:nurse,technician,admin,super_admin'],
            'unit_id' => ['nullable', 'required_if:role,nurse', 'exists:units,id'],
        ]);

        $data['password'] = Hash::make($data['password']);

        if ($data['role'] !== 'nurse') {
            $data['unit_id'] = null;
        }

        User::query()->create($data);

        return back()->with('status', 'Data pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:nurse,technician,admin,super_admin'],
            'unit_id' => ['nullable', 'required_if:role,nurse', 'exists:units,id'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        if ($data['role'] !== 'nurse') {
            $data['unit_id'] = null;
        }

        $user->update($data);

        return back()->with('status', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['email' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.']);
        }

        $user->delete();

        return back()->with('status', 'Data pengguna berhasil dihapus.');
    }
}

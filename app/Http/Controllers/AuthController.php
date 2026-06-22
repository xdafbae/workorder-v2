<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectFor(Auth::user()));
    }

    public function demo(Request $request, string $role): RedirectResponse
    {
        $user = User::query()->where('role', $role)->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($this->redirectFor($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegister(): View
    {
        $units = Unit::query()->orderBy('name')->get();
        return view('auth.register', [
            'units' => $units,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:nurse,technician,admin'],
            'unit_id' => ['nullable', 'required_if:role,nurse', 'exists:units,id'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $data['password'] = Hash::make($data['password']);

        if ($data['role'] !== 'nurse') {
            $data['unit_id'] = null;
        }

        $user = User::query()->create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($this->redirectFor($user));
    }

    private function redirectFor(User $user): string
    {
        return match ($user->role) {
            'technician' => route('dashboard.technician', absolute: false),
            'admin', 'super_admin' => route('dashboard.admin', absolute: false),
            default => route('dashboard.nurse', absolute: false),
        };
    }
}

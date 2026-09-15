<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Gunakan format email yang benar.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $credentials['active'] = true;

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email atau kata sandi belum sesuai.']);
        }

        $request->session()->regenerate();

        // Clear stale intended URLs to prevent 403 "session hanging" bugs
        $request->session()->forget('url.intended');

        $user = Auth::user();
        $role = $user->role?->value ?? null;

        return match ($role) {
            UserRole::ADMIN_SEKOLAH->value => redirect()->route('dashboard'),
            UserRole::GURU->value => redirect()->route('student.dashboard'),
            UserRole::SISWA->value => redirect()->route('student.dashboard'),
            default => back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Peran pengguna tidak dikenali. Hubungi administrator.']),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->flush();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

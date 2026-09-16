<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $role = $request->query('role', 'siswa');
        $school = SchoolSetting::firstOrFail();

        if ($role === 'siswa' && !$school->registration_enabled_students) {
            abort(403, 'Pendaftaran siswa saat ini ditutup.');
        }

        if ($role === 'guru' && !$school->registration_enabled_teachers) {
            abort(403, 'Pendaftaran guru saat ini ditutup.');
        }

        return view('auth.register', compact('role', 'school'));
    }

    public function register(Request $request)
    {
        $school = SchoolSetting::firstOrFail();
        $role = $request->input('role', 'siswa');

        if ($role === 'siswa' && !$school->registration_enabled_students) {
            return back()->with('error', 'Pendaftaran siswa sudah ditutup.');
        }

        if ($role === 'guru' && !$school->registration_enabled_teachers) {
            return back()->with('error', 'Pendaftaran guru sudah ditutup.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'identifier' => 'required|string|max:30|unique:users,identifier',
            'class_name' => $role === 'siswa' ? 'required|string|max:50' : 'nullable|string|max:50',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:siswa,guru',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'identifier' => $data['identifier'],
            'class_name' => $data['class_name'] ?? ($role === 'guru' ? 'Staf Pengajar' : null),
            'password' => Hash::make($data['password']),
            'role' => $role === 'siswa' ? UserRole::SISWA : UserRole::GURU,
            'active' => true,
        ]);

        auth()->login($user);

        return redirect()->route('student.dashboard')->with('ok', 'Selamat datang! Pendaftaran berhasil.');
    }
}

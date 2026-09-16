@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md mx-auto bg-white rounded-3xl shadow-2xl border border-white overflow-hidden animate-[fadeIn_.6s_ease]">
    <div class="bg-gradient-to-br from-[#0f1e3d] via-[#1a3a7a] to-[#2c68f5] p-8 text-center text-white relative">
        <div class="absolute -top-10 -right-10 h-32 w-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <img src="{{ asset('images/logo-smk.png') }}" class="h-14 w-14 mx-auto mb-4 bg-white p-1.5 rounded-2xl shadow-xl" alt="Logo">
            <h1 class="text-xl font-black uppercase tracking-widest">Daftar Akun Baru</h1>
            <p class="text-xs font-medium opacity-70 mt-1">Sistem Absensi Digital · SMK BINA UTAMA</p>
        </div>
    </div>

    <div class="p-8">
        <div class="mb-6 flex gap-2">
            <div class="flex-1 p-3 rounded-2xl {{ $role === 'siswa' ? 'bg-blue-50 border border-blue-100 text-[#2c68f5]' : 'bg-slate-50 text-slate-400' }} text-center">
                <p class="text-[10px] font-black uppercase tracking-wider">Sebagai Siswa</p>
            </div>
            <div class="flex-1 p-3 rounded-2xl {{ $role === 'guru' ? 'bg-purple-50 border border-purple-100 text-purple-600' : 'bg-slate-50 text-slate-400' }} text-center">
                <p class="text-[10px] font-black uppercase tracking-wider">Sebagai Guru</p>
            </div>
        </div>

        <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            <div>
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Nama Lengkap</label>
                <div class="relative mt-1.5">
                    <i class="ti ti-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input name="name" type="text" value="{{ old('name') }}" required class="w-full h-12 bg-slate-50 border border-slate-100 rounded-2xl pl-11 pr-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition shadow-inner" placeholder="Masukkan nama lengkap">
                </div>
                @error('name')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Alamat Email</label>
                <div class="relative mt-1.5">
                    <i class="ti ti-mail absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input name="email" type="email" value="{{ old('email') }}" required class="w-full h-12 bg-slate-50 border border-slate-100 rounded-2xl pl-11 pr-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition shadow-inner" placeholder="email@sekolah.com">
                </div>
                @error('email')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">{{ $role === 'siswa' ? 'NISN' : 'NIP / ID' }}</label>
                    <input name="identifier" type="text" value="{{ old('identifier') }}" required class="w-full h-12 bg-slate-50 border border-slate-100 rounded-2xl px-4 mt-1.5 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition shadow-inner">
                    @error('identifier')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">{{ $role === 'siswa' ? 'Kelas' : 'Unit' }}</label>
                    <input name="class_name" type="text" value="{{ old('class_name') }}" {{ $role === 'siswa' ? 'required' : '' }} class="w-full h-12 bg-slate-50 border border-slate-100 rounded-2xl px-4 mt-1.5 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition shadow-inner" placeholder="X TKJ 1">
                    @error('class_name')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Sandi</label>
                    <input name="password" type="password" required class="w-full h-12 bg-slate-50 border border-slate-100 rounded-2xl px-4 mt-1.5 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition shadow-inner">
                    @error('password')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Konfirmasi</label>
                    <input name="password_confirmation" type="password" required class="w-full h-12 bg-slate-50 border border-slate-100 rounded-2xl px-4 mt-1.5 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition shadow-inner">
                </div>
            </div>

            <button type="submit" class="w-full h-14 bg-gradient-to-r from-[#0f1e3d] to-[#2c68f5] text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-500/25 mt-6 active:scale-95 transition-all">
                Daftar & Masuk Sistem
            </button>
        </form>

        <p class="mt-8 text-center text-xs font-bold text-slate-400 uppercase tracking-widest">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-[#2c68f5] border-b-2 border-blue-100">Masuk Sekarang</a>
        </p>
    </div>
</div>
@endsection

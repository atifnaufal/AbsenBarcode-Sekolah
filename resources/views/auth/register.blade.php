@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md mx-auto px-4" x-data="registerPage()">
    {{-- Smart Loading Overlay --}}
    <div x-show="isSubmitting" x-transition.opacity class="fixed inset-0 z-[100] bg-[#0f1e3d]/90 backdrop-blur-xl flex flex-col items-center justify-center text-white text-center p-6" style="display: none;">
        <div class="relative mb-8">
            <div class="h-24 w-24 rounded-[40px] bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] p-1 animate-pulse">
                <div class="h-full w-full rounded-[36px] bg-[#0f1e3d] flex items-center justify-center">
                    <i class="ti ti-user-plus text-4xl text-[#ffd500] animate-bounce"></i>
                </div>
            </div>
            <div class="absolute inset-0 rounded-[40px] border-4 border-white/20 animate-ping"></div>
        </div>

        <h2 class="text-xl font-black uppercase tracking-[0.2em] text-[#ffd500]">Menyusun Identitas...</h2>
        <p class="text-xs text-white/60 mt-2 font-medium max-w-[200px]">Sedang mendaftarkan akun Anda ke sistem absensi digital.</p>

        <div class="mt-8 flex gap-1">
            <span class="h-1.5 w-1.5 rounded-full bg-[#ffd500] animate-bounce"></span>
            <span class="h-1.5 w-1.5 rounded-full bg-[#ffd500] animate-bounce [animation-delay:0.2s]"></span>
            <span class="h-1.5 w-1.5 rounded-full bg-[#ffd500] animate-bounce [animation-delay:0.4s]"></span>
        </div>
    </div>

    <div class="bg-white rounded-[40px] shadow-2xl border border-white overflow-hidden animate-[fadeIn_.6s_ease] relative">
        {{-- Decorative Background --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 bg-blue-50 rounded-full blur-3xl opacity-60"></div>

        <div class="bg-gradient-to-br from-[#0f1e3d] via-[#1a3a7a] to-[#2c68f5] p-10 text-center text-white relative">
            <div class="relative z-10">
                <div class="inline-flex h-16 w-16 items-center justify-center bg-white p-2.5 rounded-3xl shadow-2xl mb-4 transform hover:rotate-6 transition-transform">
                    <img src="{{ asset('images/logo-smk.png') }}" class="h-full w-full object-contain" alt="Logo">
                </div>
                <h1 class="text-2xl font-black uppercase tracking-widest leading-none">Registrasi</h1>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mt-2">Siswa & Guru Digital</p>
            </div>
        </div>

        <div class="p-8 sm:p-10">
            {{-- Quick Role Indicator --}}
            <div class="mb-8 flex items-center justify-center gap-4">
                <div class="flex items-center gap-2 px-4 py-2 rounded-2xl {{ $role === 'siswa' ? 'bg-blue-50 text-[#2c68f5] border border-blue-100' : 'opacity-30' }}">
                    <i class="ti ti-school text-lg"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Siswa</span>
                </div>
                <div class="h-1 w-8 bg-slate-100 rounded-full"></div>
                <div class="flex items-center gap-2 px-4 py-2 rounded-2xl {{ $role === 'guru' ? 'bg-purple-50 text-purple-600 border border-purple-100' : 'opacity-30' }}">
                    <i class="ti ti-users text-lg"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Guru</span>
                </div>
            </div>

            <form action="{{ route('register.store') }}" method="POST" class="space-y-5" @submit="handleSubmit">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                {{-- Input: Name --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">Nama Lengkap</label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#2c68f5] transition-colors">
                            <i class="ti ti-user text-lg"></i>
                        </div>
                        <input name="name" type="text" value="{{ old('name') }}" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all shadow-inner" placeholder="Nama sesuai Dapodik">
                    </div>
                    @error('name')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                {{-- Input: Email --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">Email Aktif</label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#2c68f5] transition-colors">
                            <i class="ti ti-mail text-lg"></i>
                        </div>
                        <input name="email" type="email" value="{{ old('email') }}" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all shadow-inner" placeholder="email@sekolah.com">
                    </div>
                    @error('email')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                {{-- Grid: ID & Class --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">{{ $role === 'siswa' ? 'NISN' : 'NIP / ID' }}</label>
                        <input name="identifier" type="text" value="{{ old('identifier') }}" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all shadow-inner">
                        @error('identifier')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">{{ $role === 'siswa' ? 'Kelas' : 'Wali Kelas' }}</label>
                        <input name="class_name" type="text" value="{{ old('class_name') }}" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all shadow-inner" placeholder="{{ $role === 'siswa' ? 'X RPL 1' : 'Contoh: XI RPL' }}">
                        @error('class_name')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Grid: Password --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">Sandi</label>
                        <input name="password" type="password" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition-all shadow-inner">
                        @error('password')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pl-1">Konfirmasi</label>
                        <input name="password_confirmation" type="password" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition-all shadow-inner">
                    </div>
                </div>

                <button type="submit" class="w-full h-16 bg-gradient-to-r from-[#0f1e3d] to-[#2c68f5] text-white font-black text-xs uppercase tracking-[0.25em] rounded-[24px] shadow-2xl shadow-blue-500/30 mt-4 active:scale-95 active:shadow-lg transition-all duration-200">
                    Selesaikan Pendaftaran
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-50 text-center">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                    Sudah Terdaftar? <a href="{{ route('login') }}" class="text-[#2c68f5] border-b-2 border-blue-100 ml-1">Masuk Akun</a>
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8 text-center">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">Integrated Absensi V2.0</p>
    </div>
</div>

<script>
    function registerPage() {
        return {
            isSubmitting: false,
            handleSubmit() {
                this.isSubmitting = true;
            }
        }
    }
</script>
@endsection


@extends('layouts.auth')

@section('content')
<div class="w-full max-w-[960px] mx-auto grid lg:grid-cols-[1.1fr_.9fr] overflow-hidden rounded-2xl bg-white shadow-[0_16px_40px_rgba(17,26,49,.14)] border border-white/60 animate-[fadeIn_.6s_ease]">
    {{-- LEFT: 3D hero - desktop only --}}
    <section class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-[#0f1e3d] p-8 xl:p-10 2xl:p-12" aria-label="SMK Bina Utama">
        <div class="absolute inset-0">
            <img src="{{ asset('images/bg-sekolah.jpg') }}" alt="" class="h-full w-full object-cover opacity-[0.32]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0d1a3a]/90 via-[#0f2a5e]/70 to-[#1a3a7a]/60"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
            <div class="absolute -top-10 -left-10 h-52 w-52 rounded-full bg-[#2c68f5]/30 blur-[40px] animate-[float_6s_ease-in-out_infinite]"></div>
            <div class="absolute bottom-20 right-10 h-64 w-64 rounded-full bg-[#ffd500]/20 blur-[45px] animate-[float_7s_ease-in-out_infinite_reverse]"></div>
        </div>
        <div class="relative">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 xl:h-[52px] xl:w-[52px] shrink-0 rounded-2xl bg-white p-[5px] shadow-[0_12px_28px_rgba(0,0,0,.3)] flex items-center justify-center animate-[tilt3d_5s_ease-in-out_infinite]" style="transform: perspective(600px) rotateY(-8deg) rotateX(6deg);">
                    <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK Bina Utama Kendal" class="h-full w-full object-contain drop-shadow">
                </div>
                <div class="text-white min-w-0">
                    <div class="school-display text-xs xl:text-[13px] font-bold tracking-[0.12em]">SMK BINA UTAMA</div>
                    <div class="text-[10px] xl:text-[11px] tracking-[0.16em] text-white/70 font-semibold">KENDAL • SEKOLAH MENENGAH KEJURUAN</div>
                </div>
            </div>
            <div class="mt-10 xl:mt-14 max-w-[420px]">
                <p class="inline-flex items-center rounded-full bg-white/10 backdrop-blur px-3 py-1.5 text-[10px] font-bold tracking-[0.16em] text-[#a9c1ff] border border-white/15">● PUSAT KEHADIRAN DIGITAL</p>
                <h1 class="school-display mt-4 xl:mt-5 text-[28px] xl:text-[36px] font-bold leading-[1.05] text-white drop-shadow-[0_2px_12px_rgba(0,0,0,.3)]">Absensi<br><span class="bg-gradient-to-r from-[#ffd500] to-[#ffc017] bg-clip-text text-transparent">cerdas, aman,</span><br>siap dipindai.</h1>

            </div>
        </div>

    </section>

    {{-- RIGHT: form --}}
    <section class="relative bg-white p-5 sm:p-8 xl:p-10 flex flex-col justify-center">
        {{-- Mobile header with image banner --}}
        <div class="lg:hidden -mx-5 -mt-5 sm:-mx-8 sm:-mt-8 mb-6 overflow-hidden">
            <div class="relative h-32 sm:h-40">
                <img src="{{ asset('images/bg-sekolah.jpg') }}" alt="" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0f1e3d] via-[#0f1e3d]/60 to-transparent"></div>
                <div class="absolute bottom-3 left-4 right-4 flex items-center gap-3">
                    <img src="{{ asset('images/logo-smk.png') }}" alt="Logo" class="h-10 w-10 sm:h-11 sm:w-11 rounded-xl bg-white p-1.5 shadow-lg shrink-0 object-contain">
                    <div class="text-white min-w-0">
                        <div class="school-display text-xs sm:text-sm font-bold leading-tight">SMK BINA UTAMA KENDAL</div>
                        <div class="text-[11px] text-white/75">Absensi Digital Module V.1.1.0 •</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden xl:block absolute -top-6 -right-6 opacity-[0.06] pointer-events-none">
            <img src="{{ asset('images/logo-smk.png') }}" class="h-36 w-36 object-contain" alt="">
        </div>

        <div class="mb-5 sm:mb-7">
            <p class="text-[10px] font-bold tracking-[0.18em] text-[#623ed8] uppercase">Selamat datang Siswa & Guru </p>
            <h2 class="school-display mt-1.5 text-[22px] sm:text-[26px] font-bold leading-tight text-[#0f1e3d]">Masuk ke ruang absensi</h2>
            <p class="mt-2 text-[13px] sm:text-[13.5px] leading-6 text-[#68748b]">Gunakan akun absensi digital yang sudah terdaftar</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2.5 text-sm text-red-700" role="alert">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.store') }}" method="POST" class="space-y-4 sm:space-y-5" x-data="firebaseAuth({ enabled: {{ config('firebase_integration.web.auth_enabled') ? 'true' : 'false' }}, sessionUrl: '{{ route('firebase.session') }}' })" @submit.prevent="submit($event)">
            @csrf
            <div x-show="enabled" x-cloak class="rounded-xl border border-violet-200 bg-violet-50 px-3.5 py-2.5 text-xs text-violet-700">Firebase aktif — {{ config('firebase_integration.project_id') }}.</div>
            <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 px-3.5 py-2.5 text-xs text-red-600" x-text="error"></div>

            <div>
                <label for="email" class="mb-1.5 block text-[13px] font-bold text-[#172033]">Email</label>
                <div class="relative">
                    <i class="ti ti-mail absolute left-3.5 top-1/2 -translate-y-1/2 text-[#8a95a8] text-sm"></i>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@sekolah.test" class="h-11 w-full rounded-xl border border-[#e3e8f0] bg-white pl-10 pr-3 text-sm outline-none focus:border-[#2c68f5] focus:ring-[3px] focus:ring-[#2c68f5]/10">
                </div>
                @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <div class="mb-1.5 flex items-center justify-between gap-2"><label for="password" class="text-[13px] font-bold text-[#172033]">Kata sandi</label></div>
                <div class="relative">
                    <i class="ti ti-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-[#8a95a8] text-sm"></i>
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••" class="h-11 w-full rounded-xl border border-[#e3e8f0] bg-white pl-10 pr-3 text-sm outline-none focus:border-[#2c68f5] focus:ring-[3px] focus:ring-[#2c68f5]/10">
                </div>
                @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-2 text-xs sm:text-sm text-[#68748b] font-medium"><input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-[#e3e8f0] text-[#315fea] focus:ring-[#2c68f5]/20">Ingat perangkat ini</label>

            <button type="submit" class="w-full h-11 rounded-xl bg-gradient-to-r from-[#1a3a7a] to-[#2c68f5] text-white font-bold text-sm shadow-[0_8px_20px_rgba(44,104,245,.35)] hover:shadow-[0_10px_28px_rgba(44,104,245,.45)] active:translate-y-[1px] transition flex items-center justify-center gap-2" :disabled="loading">
                <i class="ti" :class="loading ? 'ti-loader-2 animate-spin' : 'ti-arrow-right'"></i>
                <span x-text="loading ? 'Memverifikasi…' : (enabled ? 'Masuk dengan Firebase' : 'Masuk ke aplikasi')">Masuk ke aplikasi</span>
            </button>
        </form>

        {{-- NEW: Dynamic Registration Links --}}
        @php $school = \App\Models\SchoolSetting::first(); @endphp
        @if($school && ($school->registration_enabled_students || $school->registration_enabled_teachers))
        <div class="mt-8 pt-6 border-t border-slate-100 space-y-4">
            <p class="text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Belum Terdaftar?</p>
            <div class="flex flex-col sm:flex-row gap-3">
                @if($school->registration_enabled_students)
                <a href="{{ route('register', ['role' => 'siswa']) }}" class="flex-1 h-12 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center text-[10px] font-black text-[#2c68f5] uppercase tracking-widest hover:bg-blue-100 transition">
                    Daftar Sebagai Siswa
                </a>
                @endif
                @if($school->registration_enabled_teachers)
                <a href="{{ route('register', ['role' => 'guru']) }}" class="flex-1 h-12 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-[10px] font-black text-purple-600 uppercase tracking-widest hover:bg-purple-100 transition">
                    Daftar Sebagai Guru
                </a>
                @endif
            </div>
        </div>
        @endif

        <p class="mt-6 text-center text-[11px] text-[#8a95a8]">© {{ date('Y') }} SMK Bina Utama Kendal</p>
    </section>
</div>
@endsection

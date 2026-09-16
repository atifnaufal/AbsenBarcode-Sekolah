@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-[#f8fafd] text-[#0f172a] font-sans relative" x-data="profilePage()">

    {{-- Global Loading Overlay for Logout --}}
    <div x-show="isLoggingOut" x-transition.opacity class="fixed inset-0 z-[100] bg-[#0f1e3d]/90 backdrop-blur-lg flex flex-col items-center justify-center text-white" style="display: none;">
        <div class="h-20 w-20 rounded-3xl bg-white/10 flex items-center justify-center mb-4 shadow-2xl border border-white/20">
            <i class="ti ti-loader-2 animate-spin text-4xl text-[#ffd500]"></i>
        </div>
        <p class="text-sm font-black uppercase tracking-[0.2em] text-[#ffd500]">Menghancurkan Sesi...</p>
        <p class="text-[10px] font-bold opacity-60 mt-2">Sedang keluar dengan aman</p>
    </div>

    {{-- Top Header Section --}}
    <div class="fixed top-0 left-0 w-full h-[180px] bg-gradient-to-br from-[#0f1e3d] via-[#1a3a7a] to-[#2c68f5] rounded-b-[40px] shadow-2xl z-0"></div>

    <div class="mx-auto max-w-md px-5 pt-8 pb-32 relative z-10">

        {{-- Page Title --}}
        <div class="flex items-center gap-3 mb-6 text-white">
            <a href="{{ route('student.dashboard') }}" class="h-10 w-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20">
                <i class="ti ti-chevron-left text-lg"></i>
            </a>
            <h1 class="text-xl font-black font-display tracking-tight">Pengaturan Profil</h1>
        </div>

        @if(session('ok'))
        <div class="animate-slideIn rounded-2xl bg-emerald-500 text-white px-5 py-3 text-xs font-bold flex items-center gap-3 shadow-lg mb-6">
            <i class="ti ti-circle-check text-lg"></i>
            <span class="flex-1">{{ session('ok') }}</span>
        </div>
        @endif

        {{-- Profile Header Card --}}
        <div class="bg-white rounded-[32px] p-6 shadow-xl border border-white mb-6 text-center">
            <div class="relative inline-block mb-4">
                <div class="h-24 w-24 rounded-3xl bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] p-1 shadow-xl mx-auto">
                    <div class="h-full w-full rounded-[20px] bg-[#0f1e3d] flex items-center justify-center font-display font-black text-3xl text-white">
                        {{ str(auth()->user()->name)->substr(0,1)->upper() }}
                    </div>
                </div>
                <div class="absolute -bottom-1 -right-1 h-8 w-8 rounded-full bg-emerald-500 border-4 border-white flex items-center justify-center text-white text-xs shadow-lg">
                    <i class="ti ti-shield-check"></i>
                </div>
            </div>
            <h2 class="text-xl font-black font-display text-[#0f1e3d]">{{ auth()->user()->name }}</h2>
            <p class="text-xs font-bold text-[#64748b] mt-1 uppercase tracking-widest">{{ auth()->user()->role?->label() }}</p>

            <div class="flex items-center justify-center gap-2 mt-4">
                <span class="px-3 py-1 rounded-full bg-[#f1f5f9] text-[10px] font-black text-[#64748b] border border-[#e2e8f0]">{{ auth()->user()->identifier }}</span>
                @if(auth()->user()->class_name)
                <span class="px-3 py-1 rounded-full bg-[#f1f5f9] text-[10px] font-black text-[#2c68f5] border border-[#e2e8f0]">{{ auth()->user()->class_name }}</span>
                @endif
            </div>
        </div>

        {{-- NEW: Interactive FAQ Section --}}
        <div class="bg-white rounded-[32px] p-6 shadow-xl border border-white mb-6" x-data="{ openFaq: null }">
            <h3 class="text-[10px] font-black text-[#2c68f5] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                <i class="ti ti-help-circle text-sm"></i> Pusat Bantuan & FAQ
            </h3>

            <div class="divide-y divide-slate-50">
                {{-- FAQ 1 --}}
                <div class="py-3">
                    <button @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full flex items-center justify-between text-left group">
                        <span class="text-xs font-bold text-[#0f1e3d] group-hover:text-[#2c68f5] transition-colors">Bagaimana cara absen yang benar?</span>
                        <i class="ti text-[#94a3b8] transition-transform duration-300" :class="openFaq === 1 ? 'ti-chevron-up rotate-180 text-[#2c68f5]' : 'ti-chevron-down'"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse x-cloak class="mt-2 text-[11px] text-[#64748b] leading-relaxed">
                        Pastikan GPS perangkat aktif, berikan izin lokasi di browser, dan arahkan kamera ke QR Code yang tampil di Monitor Sekolah. Pastikan Anda berada dalam radius aman sekolah.
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="py-3">
                    <button @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full flex items-center justify-between text-left group">
                        <span class="text-xs font-bold text-[#0f1e3d] group-hover:text-[#2c68f5] transition-colors">Kenapa lokasi saya tidak terbaca?</span>
                        <i class="ti text-[#94a3b8] transition-transform duration-300" :class="openFaq === 2 ? 'ti-chevron-up rotate-180 text-[#2c68f5]' : 'ti-chevron-down'"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse x-cloak class="mt-2 text-[11px] text-[#64748b] leading-relaxed">
                        Hal ini biasanya terjadi karena izin lokasi diblokir atau GPS tidak akurat. Coba muat ulang halaman, pastikan anda tidak menggunakan VPN, dan berada di area terbuka untuk akurasi GPS maksimal.
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="py-3">
                    <button @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full flex items-center justify-between text-left group">
                        <span class="text-xs font-bold text-[#0f1e3d] group-hover:text-[#2c68f5] transition-colors">Mengapa QR Code tidak muncul?</span>
                        <i class="ti text-[#94a3b8] transition-transform duration-300" :class="openFaq === 3 ? 'ti-chevron-up rotate-180 text-[#2c68f5]' : 'ti-chevron-down'"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse x-cloak class="mt-2 text-[11px] text-[#64748b] leading-relaxed">
                        Monitor hanya menampilkan QR Code pada jam operasional yang telah ditentukan sekolah. Jika di luar jadwal, sistem akan otomatis menutup akses absensi.
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-50 flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="ti ti-brand-whatsapp text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-[#0f1e3d]">Butuh Bantuan Lain?</p>
                    <p class="text-[9px] text-[#94a3b8] font-bold uppercase tracking-tighter">Hubungi Admin IT Sekolah</p>
                </div>
            </div>
        </div>

        {{-- Form Section --}}
        <form method="POST" action="{{ route('student.profile.update') }}" @submit="handleSubmit" class="space-y-6">
            @csrf @method('PUT')

            <div class="bg-white rounded-[32px] p-6 shadow-xl border border-white space-y-5">
                <h3 class="text-[10px] font-black text-[#2c68f5] uppercase tracking-[0.2em] mb-4">Informasi Personal</h3>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-[#64748b] uppercase tracking-wider pl-1">Nama Lengkap</label>
                        <input name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full h-12 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none transition">
                        @error('name')<p class="text-[10px] text-red-500 font-bold pl-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-[#64748b] uppercase tracking-wider pl-1">Email Aktif</label>
                        <input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full h-12 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none transition">
                        @error('email')<p class="text-[10px] text-red-500 font-bold pl-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-[#f1f5f9]">
                    <h3 class="text-[10px] font-black text-[#2c68f5] uppercase tracking-[0.2em] mb-4">Keamanan Akun</h3>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-[#64748b] uppercase tracking-wider pl-1">Kata Sandi Baru</label>
                            <input name="password" type="password" class="w-full h-12 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none transition" placeholder="Biarkan kosong jika tidak diubah">
                            @error('password')<p class="text-[10px] text-red-500 font-bold pl-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-[#64748b] uppercase tracking-wider pl-1">Konfirmasi Kata Sandi</label>
                            <input name="password_confirmation" type="password" class="w-full h-12 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none transition" placeholder="Ulangi kata sandi baru">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <button type="submit" class="w-full h-14 bg-gradient-to-r from-[#2c68f5] to-[#1a3a7a] text-white font-black text-sm rounded-[24px] shadow-xl shadow-[#2c68f5]/25 flex items-center justify-center gap-2" :disabled="isSubmitting || isLoggingOut">
                    <i class="ti" :class="isSubmitting ? 'ti-loader animate-spin' : 'ti-device-floppy'"></i>
                    <span x-text="isSubmitting ? 'Memproses...' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" id="logoutForm" @submit.prevent="confirmLogout">
            @csrf
            <button type="submit" class="w-full h-14 bg-white border border-red-100 text-red-500 font-black text-sm rounded-[24px] shadow-lg flex items-center justify-center gap-2 mt-4 transition active:scale-95 hover:bg-red-50" :disabled="isSubmitting || isLoggingOut">
                <i class="ti ti-logout-2 text-xl"></i>
                Logout & Keluar Aplikasi
            </button>
        </form>
    </div>

    {{-- Premium Bottom Tab Bar --}}
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[280px] bg-white/80 backdrop-blur-2xl border border-white/20 rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.2)] p-2 z-50">
        <nav class="flex items-center justify-between">
            <a href="{{ route('student.dashboard') }}" class="h-12 w-12 flex items-center justify-center rounded-full transition {{ request()->routeIs('student.dashboard') ? 'bg-[#2c68f5] text-white shadow-lg shadow-[#2c68f5]/40' : 'text-[#94a3b8] hover:text-[#0f1e3d]' }}">
                <i class="ti ti-smart-home text-xl"></i>
            </a>
            <a href="{{ route('attendance.scan') }}" class="h-14 w-14 -mt-10 flex items-center justify-center rounded-full bg-gradient-to-br from-[#ffd500] to-[#ff9900] text-[#0f1e3d] shadow-xl shadow-[#ff9900]/40 border-4 border-white transform transition hover:scale-110 active:scale-95">
                <i class="ti ti-qrcode text-2xl"></i>
            </a>
            <a href="{{ route('student.profile') }}" class="h-12 w-12 flex items-center justify-center rounded-full transition {{ request()->routeIs('student.profile') ? 'bg-[#2c68f5] text-white shadow-lg shadow-[#2c68f5]/40' : 'text-[#94a3b8] hover:text-[#0f1e3d]' }}">
                <i class="ti ti-user-square-rounded text-xl"></i>
            </a>
        </nav>
    </div>
</div>

<script>
function profilePage() {
    return {
        isSubmitting: false,
        isLoggingOut: false,
        handleSubmit() {
            this.isSubmitting = true;
        },
        confirmLogout() {
            if (confirm('Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem absensi?')) {
                this.isLoggingOut = true;
                setTimeout(() => {
                    document.getElementById('logoutForm').submit();
                }, 1000);
            }
        }
    }
}
</script>
@endsection

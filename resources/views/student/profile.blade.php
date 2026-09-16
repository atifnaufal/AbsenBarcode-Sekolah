@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-[#f8fafd] text-[#0f172a] font-sans relative" x-data="profilePage()">

    {{-- Global Loading Overlay --}}
    <div x-show="isLoggingOut" x-transition.opacity class="fixed inset-0 z-[100] bg-[#0f1e3d]/90 backdrop-blur-lg flex flex-col items-center justify-center text-white" style="display: none;">
        <div class="h-20 w-20 rounded-3xl bg-white/10 flex items-center justify-center mb-4 shadow-2xl border border-white/20">
            <i class="ti ti-loader-2 animate-spin text-4xl text-[#ffd500]"></i>
        </div>
        <p class="text-sm font-black uppercase tracking-[0.2em] text-[#ffd500]">Menghancurkan Sesi...</p>
    </div>

    {{-- Top Dynamic Background --}}
    <div class="fixed top-0 left-0 w-full bg-gradient-to-br from-[#0f1e3d] via-[#1a3a7a] to-[#2c68f5] rounded-b-[40px] shadow-2xl z-0 transition-all duration-500" :class="state !== 'menu' ? 'h-[120px]' : 'h-[220px]'"></div>

    <div class="mx-auto max-w-md px-5 pt-8 pb-32 relative z-10">

        {{-- VIEW: MAIN SETTINGS MENU --}}
        <div x-show="state === 'menu'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <header class="mb-6 text-white">
                <h1 class="text-2xl font-black font-display tracking-tight">Akun & Pengaturan</h1>
                <p class="text-xs font-medium opacity-60 mt-1">Kelola informasi personal & keamanan Anda</p>
            </header>

            @if(session('ok'))
            <div class="animate-slideIn rounded-2xl bg-emerald-500 text-white px-5 py-3 text-xs font-bold flex items-center gap-3 shadow-lg mb-6">
                <i class="ti ti-circle-check text-lg"></i>
                <span class="flex-1">{{ session('ok') }}</span>
            </div>
            @endif

            {{-- User Hero Card --}}
            <div class="bg-white rounded-[32px] p-6 shadow-xl border border-white mb-8 text-center relative overflow-hidden">
                <div class="absolute -top-4 -right-4 h-24 w-24 bg-slate-50 rounded-full opacity-50"></div>
                <div class="relative inline-block mb-4">
                    <div class="h-24 w-24 rounded-3xl bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] p-1 shadow-xl mx-auto">
                        <div class="h-full w-full rounded-[20px] bg-[#0f1e3d] flex items-center justify-center font-display font-black text-3xl text-white overflow-hidden">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" class="h-full w-full object-cover">
                            @else
                                {{ str(auth()->user()->name)->substr(0,1)->upper() }}
                            @endif
                        </div>
                    </div>
                    <div class="absolute -bottom-1 -right-1 h-8 w-8 rounded-full bg-emerald-500 border-4 border-white flex items-center justify-center text-white text-[10px] shadow-lg">
                        <i class="ti ti-check"></i>
                    </div>
                </div>
                <h2 class="text-xl font-black font-display text-[#0f1e3d]">{{ auth()->user()->name }}</h2>
                <div class="flex items-center justify-center gap-2 mt-2">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-[9px] font-black text-slate-500 uppercase tracking-widest border border-slate-200">{{ auth()->user()->role?->label() }}</span>
                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-[9px] font-black text-[#2c68f5] uppercase tracking-widest border border-blue-100">{{ auth()->user()->identifier }}</span>
                </div>
            </div>

            {{-- Navigation Menu List --}}
            <div class="space-y-4">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-2">Data & Privasi</h3>

                <div class="bg-white rounded-[32px] shadow-lg border border-white overflow-hidden divide-y divide-slate-50">
                    {{-- Edit Profile Item --}}
                    <button @click="state = 'edit'" class="w-full flex items-center gap-4 p-5 hover:bg-slate-50 transition active:bg-slate-100 group">
                        <div class="h-10 w-10 rounded-2xl bg-blue-50 text-[#2c68f5] flex items-center justify-center text-xl shadow-inner">
                            <i class="ti ti-user-edit"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-xs font-black text-[#0f1e3d]">Informasi Pribadi</p>
                            <p class="text-[10px] font-medium text-slate-400 mt-0.5">Nama, Email, & Identitas</p>
                        </div>
                        <i class="ti ti-chevron-right text-slate-300 group-hover:translate-x-1 transition-transform"></i>
                    </button>

                    {{-- Security Item --}}
                    <button @click="state = 'security'" class="w-full flex items-center gap-4 p-5 hover:bg-slate-50 transition active:bg-slate-100 group">
                        <div class="h-10 w-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                            <i class="ti ti-shield-lock"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-xs font-black text-[#0f1e3d]">Keamanan Akun</p>
                            <p class="text-[10px] font-medium text-slate-400 mt-0.5">Kelola Kata Sandi & Akses</p>
                        </div>
                        <i class="ti ti-chevron-right text-slate-300 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>

                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-2 pt-4">Bantuan</h3>
                <div class="bg-white rounded-[32px] shadow-lg border border-white p-6" x-data="{ faq: null }">
                    <div class="space-y-4">
                        <button @click="faq = (faq === 1 ? null : 1)" class="w-full flex items-center justify-between text-left">
                            <span class="text-xs font-bold text-[#0f1e3d]">Cara Scan yang benar?</span>
                            <i class="ti ti-chevron-down text-slate-300 transition-transform" :class="faq === 1 ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="faq === 1" x-collapse class="text-[10px] text-slate-500 leading-relaxed">
                            Buka menu Scan, berikan izin GPS, dan arahkan kamera ke QR Monitor sekolah dalam radius 80m.
                        </div>

                        <button @click="faq = (faq === 2 ? null : 2)" class="w-full flex items-center justify-between text-left border-t border-slate-50 pt-4">
                            <span class="text-xs font-bold text-[#0f1e3d]">Mengapa Lokasi Gagal?</span>
                            <i class="ti ti-chevron-down text-slate-300 transition-transform" :class="faq === 2 ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="faq === 2" x-collapse class="text-[10px] text-slate-500 leading-relaxed">
                            Biasanya karena GPS lemah atau izin diblokir. Segarkan halaman atau pindah ke area yang lebih terbuka.
                        </div>
                    </div>
                </div>

                {{-- Action: Logout --}}
                <div class="pt-6">
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm" @submit.prevent="confirmLogout">
                        @csrf
                        <button type="submit" class="w-full h-14 bg-white border border-red-100 text-red-500 font-black text-[10px] uppercase tracking-widest rounded-3xl shadow-xl flex items-center justify-center gap-3 transition active:scale-95">
                            <i class="ti ti-logout-2 text-lg"></i>
                            Logout & Hancurkan Sesi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- VIEW: EDIT PROFILE SUBPAGE --}}
        <div x-show="state === 'edit'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="flex items-center gap-4 mb-8 text-white">
                <button @click="state = 'menu'" class="h-10 w-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 active:scale-90 transition shadow-lg">
                    <i class="ti ti-arrow-left text-lg"></i>
                </button>
                <h1 class="text-xl font-black font-display tracking-tight">Informasi Pribadi</h1>
            </div>

            <form method="POST" action="{{ route('student.profile.update') }}" @submit="handleSubmit" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                <div class="bg-white rounded-[32px] p-7 shadow-2xl border border-white space-y-6">
                    {{-- Avatar Upload --}}
                    <div class="flex flex-col items-center justify-center pb-4">
                        <div class="relative group cursor-pointer" @click="$refs.avatarInput.click()">
                            <div class="h-20 w-20 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden">
                                <template x-if="!avatarPreview">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="ti ti-camera text-2xl text-slate-300"></i>
                                    @endif
                                </template>
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="h-full w-full object-cover">
                                </template>
                            </div>
                            <div class="absolute -bottom-1 -right-1 h-6 w-6 rounded-lg bg-[#2c68f5] text-white flex items-center justify-center shadow-lg border-2 border-white">
                                <i class="ti ti-plus text-[10px]"></i>
                            </div>
                        </div>
                        <input type="file" name="avatar" x-ref="avatarInput" class="hidden" accept="image/*" @change="handleAvatarChange">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2">Ketuk untuk Ganti Foto</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Nama Lengkap Sesuai Dapodik</label>
                        <input name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-5 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition-all shadow-inner">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Alamat Email Aktif</label>
                        <input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-5 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition-all shadow-inner">
                    </div>
                    <div class="space-y-2 group">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">ID Identitas (Read-only)</label>
                        <div class="w-full h-14 bg-slate-100/50 border border-slate-200 rounded-2xl px-5 flex items-center text-sm font-bold text-slate-400">
                            {{ auth()->user()->identifier }}
                            <i class="ti ti-lock ml-auto text-slate-300"></i>
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-full h-15 bg-gradient-to-r from-[#2c68f5] to-[#1a3a7a] text-white font-black text-xs uppercase tracking-[0.2em] rounded-3xl shadow-xl shadow-blue-500/20 flex items-center justify-center gap-3 active:scale-95 transition-all" :disabled="isSubmitting">
                    <i class="ti" :class="isSubmitting ? 'ti-loader-2 animate-spin' : 'ti-device-floppy'"></i>
                    <span x-text="isSubmitting ? 'Sinkronisasi...' : 'SIMPAN PERUBAHAN'"></span>
                </button>
            </form>
        </div>

        {{-- VIEW: SECURITY SUBPAGE --}}
        <div x-show="state === 'security'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="flex items-center gap-4 mb-8 text-white">
                <button @click="state = 'menu'" class="h-10 w-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 active:scale-90 transition shadow-lg">
                    <i class="ti ti-arrow-left text-lg"></i>
                </button>
                <h1 class="text-xl font-black font-display tracking-tight">Keamanan & Sandi</h1>
            </div>

            <form method="POST" action="{{ route('student.profile.update') }}" @submit="handleSubmit" class="space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">

                <div class="bg-white rounded-[32px] p-7 shadow-2xl border border-white space-y-6">
                    <div class="space-y-2 text-center pb-4">
                        <div class="h-16 w-16 bg-purple-50 rounded-full flex items-center justify-center mx-auto text-purple-600 text-3xl mb-3">
                            <i class="ti ti-fingerprint"></i>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium px-4">Pastikan kata sandi Anda kuat dan tidak mudah ditebak untuk keamanan data kehadiran.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Kata Sandi Baru</label>
                        <input name="password" type="password" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-5 text-sm font-bold text-[#0f1e3d] focus:border-purple-500 focus:bg-white outline-none transition-all shadow-inner" placeholder="Min. 6 karakter">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Konfirmasi Kata Sandi</label>
                        <input name="password_confirmation" type="password" required class="w-full h-14 bg-slate-50 border border-slate-100 rounded-2xl px-5 text-sm font-bold text-[#0f1e3d] focus:border-purple-500 focus:bg-white outline-none transition-all shadow-inner" placeholder="Ketik ulang sandi">
                    </div>
                </div>
                <button type="submit" class="w-full h-15 bg-gradient-to-r from-purple-600 to-indigo-700 text-white font-black text-xs uppercase tracking-[0.2em] rounded-3xl shadow-xl shadow-purple-500/20 flex items-center justify-center gap-3 active:scale-95 transition-all" :disabled="isSubmitting">
                    <i class="ti" :class="isSubmitting ? 'ti-loader-2 animate-spin' : 'ti-shield-check'"></i>
                    <span x-text="isSubmitting ? 'Mengenkripsi...' : 'PERBARUI KATA SANDI'"></span>
                </button>
            </form>
        </div>

    </div>

    {{-- Bottom Floating Tab Bar (Responsive Native Style) --}}
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[280px] bg-white/80 backdrop-blur-2xl border border-white/20 rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.15)] p-2 z-50 transition-all duration-500" :class="state !== 'menu' ? 'opacity-0 translate-y-20 pointer-events-none' : 'opacity-100 translate-y-0'">
        <nav class="flex items-center justify-between">
            <a href="{{ route('student.dashboard') }}" class="h-12 w-12 flex items-center justify-center rounded-full transition text-slate-400 hover:text-slate-600">
                <i class="ti ti-smart-home text-xl"></i>
            </a>
            <a href="{{ route('attendance.scan') }}" class="h-14 w-14 -mt-10 flex items-center justify-center rounded-full bg-gradient-to-br from-[#ffd500] to-[#ff9900] text-[#0f1e3d] shadow-xl shadow-orange-500/30 border-4 border-white transform transition hover:scale-110 active:scale-95">
                <i class="ti ti-qrcode text-2xl"></i>
            </a>
            <div class="h-12 w-12 flex items-center justify-center rounded-full bg-[#2c68f5] text-white shadow-lg shadow-blue-500/40">
                <i class="ti ti-user-square-rounded text-xl"></i>
            </div>
        </nav>
    </div>
</div>

<script>
function profilePage() {
    return {
        state: 'menu',
        isSubmitting: false,
        isLoggingOut: false,
        avatarPreview: null,
        handleSubmit() {
            this.isSubmitting = true;
        },
        handleAvatarChange(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.avatarPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        confirmLogout() {
            if (confirm('Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?')) {
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

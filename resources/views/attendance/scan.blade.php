@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0f1e3d] via-[#111a31] to-[#1a3a7a] p-2 sm:p-6 text-white flex flex-col items-center justify-start" x-data="attendanceScanner({ scanUrl: '{{ route('attendance.scan.store') }}', demoToken: '{{ $demoQrToken }}' })">

    {{-- Decorative Hologram Light Effects --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-lg h-64 bg-[#2c68f5]/10 blur-[120px] rounded-full pointer-events-none"></div>

    {{-- Premium Voice Assistant Overlay Modal (Popup Sukses 3D) --}}
    <div x-show="showSuccessPopup" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md" style="display: none;">
        <div class="bg-gradient-to-b from-white to-[#f8f9fc] text-[#0f1e3d] rounded-3xl p-8 max-w-sm w-full text-center shadow-[0_25px_60px_-15px_rgba(0,0,0,0.5),inset_0_1px_0_rgba(255,255,255,1)] border border-white relative overflow-hidden transform">
            {{-- 3D Success Ring Elements --}}
            <div class="relative w-20 h-20 mx-auto mb-4 flex items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white text-4xl shadow-xl shadow-emerald-500/30">
                <i class="ti ti-circle-check-filled animate-pulse"></i>
            </div>

            <h2 class="text-2xl font-black font-display text-emerald-600 tracking-tight">ABSENSI BERHASIL!</h2>
            <p class="text-xs text-[#8a95a8] font-mono tracking-widest mt-1 uppercase font-bold">Mengisi Antrean Otomatis</p>

            <div class="bg-[#f2f5fa] rounded-2xl p-4 my-4 border border-school-line text-left">
                <p class="text-[10px] uppercase font-bold text-[#8a95a8] tracking-wider">Identitas Pengguna</p>
                <p class="text-sm font-black text-[#0f1e3d] mt-0.5">{{ $activeUser->name }}</p>
                <p class="text-xs text-[#68748b] font-medium mt-0.5">ID: {{ $activeUser->identifier }} · {{ $activeUser->class_name ?? 'Staf' }}</p>
            </div>

            <div class="flex items-center justify-center gap-2 text-xs font-bold text-indigo-600 bg-indigo-50 py-2.5 px-4 rounded-xl border border-indigo-100">
                <i class="ti ti-volume text-base animate-bounce"></i>
                <span id="ttsStatusMsg">Menunggu Google Voice Selesai...</span>
            </div>

            <p class="text-[10px] text-[#8a95a8] mt-4 font-semibold animate-pulse">Sistem membeku sejenak untuk menghindari double-scan</p>
        </div>
    </div>

    <div class="w-full max-w-md space-y-5 pb-24 relative z-10">

        {{-- Professional Glassmorphic Header --}}
        <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-lg">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#ffd500]">SMK Bina Utama Kendal</p>
                <h1 class="text-base font-black tracking-tight text-white mt-0.5">Pemindai QR Code</h1>
            </div>
            <span class="inline-flex px-2.5 py-1 rounded-xl text-[10px] font-bold uppercase bg-gradient-to-r from-[#2c68f5] to-[#623ed8] border border-white/10">
                Live Scanner
            </span>
        </div>

        {{-- User Identity Sub-Card --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center justify-between shadow-inner">
            <div class="min-w-0 flex-1">
                <p class="text-[10px] text-white/50 uppercase tracking-widest font-bold">Masuk Sebagai</p>
                <h2 class="text-base font-black truncate text-white mt-0.5">{{ $activeUser->name }}</h2>
                <p class="text-xs text-white/60 font-mono mt-0.5">{{ $activeUser->identifier }} • {{ $activeUser->class_name ?? 'Staf Sekolah' }}</p>
            </div>
            <div class="h-10 w-10 bg-white/10 rounded-xl flex items-center justify-center text-sm font-bold border border-white/10 text-[#ffd500]">
                {{ str($activeUser->name)->substr(0,1)->upper() }}
            </div>
        </div>

        {{-- Geolocation Tracker Card --}}
        <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] font-bold uppercase tracking-widest text-[#ffd500]">Koordinat Validasi GPS</p>
                    <h3 class="text-sm font-bold text-white mt-0.5 flex items-center gap-1.5">
                        <i class="ti ti-map-pin text-base text-[#2c68f5]"></i>
                        <span x-text="locationState === 'ready' ? '✓ Posisi GPS Terkunci' : locationState === 'loading' ? '⏳ Mengunci Satelit...' : '⚠ GPS Belum Siap'"></span>
                    </h3>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 bg-black/20 rounded font-mono text-white/80" x-show="accuracy !== null">
                    ±<span x-text="accuracy ? Math.round(accuracy) + 'm' : '-'"></span> Acc
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-[11px] font-mono">
                <div class="bg-black/20 rounded-xl p-2.5 border border-white/5 text-center">
                    <span class="text-white/40 block">Latitude</span>
                    <strong class="text-white block mt-0.5" x-text="latitude ? latitude.toFixed(6) : '--'"></strong>
                </div>
                <div class="bg-black/20 rounded-xl p-2.5 border border-white/5 text-center">
                    <span class="text-white/40 block">Longitude</span>
                    <strong class="text-white block mt-0.5" x-text="longitude ? longitude.toFixed(6) : '--'"></strong>
                </div>
            </div>

            <button type="button" @click="locate" :disabled="locationState === 'loading'" class="w-full h-11 bg-white/10 border border-white/15 text-white hover:bg-white/20 transition rounded-xl text-xs font-bold flex items-center justify-center gap-2 disabled:opacity-50">
                <i class="ti" :class="locationState === 'loading' ? 'ti-loader animate-spin' : 'ti-target-arrow'"></i>
                <span x-text="locationState === 'loading' ? 'Menghubungkan Satelit...' : 'Sinkronisasi Posisi GPS Anda'"></span>
            </button>
        </div>

        {{-- Camera Scanner Container Box --}}
        <div class="bg-gradient-to-b from-[#1a3a7a] to-[#0f1e3d] border border-white/15 rounded-3xl p-5 shadow-2xl relative overflow-hidden">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-white">Bingkai Kamera QR</h3>
                <p class="text-[11px] text-white/60 mt-0.5">Posisikan kode batang sirkular dalam area tangkap radar di bawah ini</p>
            </div>

            {{-- Capture Window Frame --}}
            <div class="relative w-full aspect-square bg-black rounded-2xl overflow-hidden border border-white/10 shadow-inner">
                <div id="qr-reader" class="w-full h-full bg-black"></div>

                {{-- Radar 3D Overlay lines --}}
                <div class="absolute inset-0 pointer-events-none border-2 border-white/10 rounded-2xl">
                    <div class="absolute inset-8 border border-dashed border-[#2c68f5]/40 rounded-xl animate-pulse"></div>
                    <div class="absolute top-2 left-2 w-6 h-6 border-t-2 border-l-2 border-[#ffd500]"></div>
                    <div class="absolute top-2 right-2 w-6 h-6 border-t-2 border-r-2 border-[#ffd500]"></div>
                    <div class="absolute bottom-2 left-2 w-6 h-6 border-b-2 border-l-2 border-[#ffd500]"></div>
                    <div class="absolute bottom-2 right-2 w-6 h-6 border-b-2 border-r-2 border-[#ffd500]"></div>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                <button type="button" @click="startCamera" :disabled="scannerState === 'scanning' || locationState !== 'ready'" class="w-full h-11 bg-gradient-to-r from-[#2c68f5] to-[#623ed8] text-white font-bold text-xs rounded-xl shadow-md disabled:opacity-40 flex items-center justify-center gap-2">
                    <i class="ti ti-camera text-sm"></i>
                    <span x-text="scannerState === 'scanning' ? 'Radar Kamera Aktif' : 'Aktifkan Kamera Pemindai'"></span>
                </button>
            </div>
        </div>

        {{-- Fallback Non-Success Result Alert Box --}}
        <div x-show="scannerState === 'result' && result !== 'success'" class="animate-slideIn bg-white rounded-2xl p-4 text-[#0f1e3d] text-center font-semibold shadow-xl border border-white/20">
            <p class="text-sm text-red-600" x-text="responseMessage"></p>
            <button @click="resetForScan" class="mt-3 px-4 py-1.5 bg-[#f2f5fa] border border-school-line text-xs font-bold rounded-lg text-[#0f1e3d]">Pindai Ulang</button>
        </div>
    </div>

    {{-- Bottom Floating Nav bar --}}
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

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function attendanceScanner({ scanUrl, demoToken }) {
    return {
        scanUrl,
        demoToken,
        scannerState: 'idle',
        locationState: 'idle',
        latitude: null,
        longitude: null,
        accuracy: null,
        qrToken: '',
        camera: null,
        cameraRunning: false,
        result: null,
        responseMessage: '',
        showSuccessPopup: false,

        async locate() {
            if (this.locationState === 'loading') return;
            this.locationState = 'loading';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.latitude = position.coords.latitude;
                    this.longitude = position.coords.longitude;
                    this.accuracy = position.coords.accuracy;
                    this.locationState = 'ready';
                },
                () => {
                    this.locationState = 'error';
                    alert('Gagal melacak koordinat GPS. Pastikan setelan lokasi perangkat aktif!');
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        },

        async startCamera() {
            if (this.locationState !== 'ready') {
                await this.locate();
            }
            if (this.locationState !== 'ready' || this.cameraRunning) return;

            this.scannerState = 'scanning';
            this.camera = new Html5Qrcode('qr-reader');

            try {
                await this.camera.start(
                    { facingMode: 'environment' },
                    { fps: 15, qrbox: { width: 250, height: 250 } },
                    async (decodedText) => {
                        this.qrToken = decodedText;
                        await this.stopCamera();
                        await this.submit();
                    },
                    () => {}
                );
                this.cameraRunning = true;
            } catch (err) {
                this.scannerState = 'error';
                alert('Modul perangkat kamera gagal diaktifkan.');
            }
        },

        async stopCamera() {
            if (this.camera && this.cameraRunning) {
                await this.camera.stop();
            }
            this.cameraRunning = false;
            this.camera = null;
        },

        async submit() {
            if (!this.qrToken || this.latitude === null || this.longitude === null) return;
            this.scannerState = 'submitting';

            try {
                const response = await fetch(this.scanUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        qr_token: this.qrToken,
                        latitude: this.latitude,
                        longitude: this.longitude,
                        accuracy: this.accuracy,
                    }),
                });

                const payload = await response.json();
                this.responseMessage = payload.message || '';

                if (payload.result === 'success') {
                    this.result = 'success';
                    this.showSuccessPopup = true;

                    // Activate Google Voice TTS (Web Speech API)
                    this.playGoogleVoiceNotification("Absen berhasil dicatat. Terima kasih!");
                } else {
                    this.result = payload.result || 'expired';
                    this.scannerState = 'result';
                }
            } catch (error) {
                this.scannerState = 'error';
                alert('Gagal tersambung dengan server absensi.');
            }
        },

        playGoogleVoiceNotification(messageText) {
            if ('speechSynthesis' in window) {
                // Cancel any ongoing speech
                window.speechSynthesis.cancel();

                const utterance = new SpeechSynthesisUtterance(messageText);
                utterance.lang = 'id-ID';
                utterance.rate = 1.0;

                // Try finding Indonesian standard/Google voice lines
                const voices = window.speechSynthesis.getVoices();
                const idVoice = voices.find(v => v.lang.includes('id') || v.name.includes('Indonesian') || v.name.includes('Google'));
                if (idVoice) utterance.voice = idVoice;

                utterance.onend = () => {
                    const statusLbl = document.getElementById('ttsStatusMsg');
                    if (statusLbl) statusLbl.innerText = "Selesai! Meregenerasi ulang...";

                    // Hold frozen state for an extra 2.5 seconds to distribute students/prevent long queues clashing
                    setTimeout(() => {
                        this.showSuccessPopup = false;
                        this.resetForScan();
                    }, 2500);
                };

                window.speechSynthesis.speak(utterance);

                // Fallback mechanism if voice synthesis fails or is muted by browser policies
                setTimeout(() => {
                    if (this.showSuccessPopup) {
                        const statusLbl = document.getElementById('ttsStatusMsg');
                        if (statusLbl) statusLbl.innerText = "Selesai! Meregenerasi ulang...";
                        setTimeout(() => {
                            this.showSuccessPopup = false;
                            this.resetForScan();
                        }, 2500);
                    }
                }, 4000);
            } else {
                // Fallback straight ahead if Speech API is not present
                setTimeout(() => {
                    this.showSuccessPopup = false;
                    this.resetForScan();
                }, 3500);
            }
        },

        useDemoToken() {
            this.qrToken = this.demoToken;
            this.submit();
        },

        resetForScan() {
            this.qrToken = '';
            this.scannerState = 'idle';
            this.result = null;
            this.showSuccessPopup = false;
        }
    }
}
</script>
@endsection

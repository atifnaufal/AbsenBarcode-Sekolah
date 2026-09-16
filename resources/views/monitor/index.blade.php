@extends('layouts.app')

@section('content')
<div
    class="min-h-screen bg-[#0f1e3d] text-white overflow-hidden relative"
    x-data="monitorCountdown({
        initial: {{ Illuminate\Support\Js::from($activeQr) }},
        refreshUrl: '{{ route('monitor.refresh') }}',
        isClosed: {{ $isClosed ? 'true' : 'false' }},
        label: '{{ $schedule['label'] }}'
    })"
    x-init="init(); startPoll('{{ route('monitor.recent') }}')"
    @monitor-error.window="window.alert($event.detail.message)"
>
    {{-- Animated Background Glows --}}
    <div class="absolute -top-40 -left-40 h-[600px] w-[600px] rounded-full bg-[#2c68f5]/10 blur-[120px] animate-pulse"></div>
    <div class="absolute -bottom-40 -right-40 h-[600px] w-[600px] rounded-full bg-[#623ed8]/10 blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>

    <div class="relative z-10 mx-auto max-w-[1440px] px-6 py-6 h-screen flex flex-col">

        {{-- Monitor Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-white rounded-2xl flex items-center justify-center p-2 shadow-xl shadow-white/5">
                    <img src="{{ asset('images/logo-smk.png') }}" class="h-full w-full object-contain" alt="Logo">
                </div>
                <div>
                    <h1 class="text-xl font-black font-display tracking-tight text-white uppercase">{{ $school->name }}</h1>
                    <p class="text-[10px] font-bold text-[#ffd500] uppercase tracking-[0.25em]">Monitor Absensi Digital · Live System</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-white/5 border border-white/10 rounded-2xl px-4 py-2 backdrop-blur-md">
                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest">Status Koneksi</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-bold font-mono">ENCRYPTED LINK · SECURED</span>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="h-12 px-6 rounded-2xl bg-white text-[#0f1e3d] font-black text-xs uppercase tracking-wider flex items-center gap-2 hover:bg-[#ffd500] transition-all group">
                    <i class="ti ti-layout-dashboard text-lg transition-transform group-hover:scale-110"></i>
                    Panel Admin
                </a>
            </div>
        </div>

        {{-- Main Monitor Content Area --}}
        <div class="flex-1 grid lg:grid-cols-2 gap-8 items-center overflow-hidden pb-10">

            {{-- Left Side: QR Display or Closed State --}}
            <div class="h-full flex items-center justify-center">

                {{-- CLOSED STATE --}}
                <div x-show="isClosed" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0" class="w-full max-w-lg text-center p-12 rounded-[40px] bg-white/5 border border-white/10 backdrop-blur-xl shadow-2xl">
                    <div class="relative w-32 h-32 mx-auto mb-8">
                        <div class="absolute inset-0 bg-[#ffd500]/20 rounded-full blur-2xl animate-pulse"></div>
                        <div class="relative h-full w-full bg-gradient-to-tr from-[#0f1e3d] to-[#1a3a7a] border border-white/20 rounded-full flex items-center justify-center text-5xl text-[#ffd500] shadow-2xl">
                            <i class="ti ti-clock-pause animate-[spin_10s_linear_infinite]"></i>
                        </div>
                    </div>
                    <h2 class="text-4xl font-black font-display text-white leading-tight">Sesi Absensi<br>Belum Aktif</h2>
                    <p class="mt-6 text-white/60 text-lg leading-relaxed max-w-md mx-auto">
                        Sistem mematikan generator QR secara otomatis saat di luar jam operasional yang ditentukan.
                    </p>
                    <div class="mt-10 p-6 rounded-3xl bg-white/5 border border-white/10 inline-block">
                        <p class="text-xs font-bold text-[#ffd500] uppercase tracking-[0.2em] mb-2">Jadwal Sesi Berikutnya</p>
                        <p class="text-2xl font-black font-mono tracking-tighter" x-text="label"></p>
                        <p class="text-3xl font-black font-mono text-white mt-2">{{ substr($schedule['start'] ?? '00:00', 0, 5) }} - {{ substr($schedule['end'] ?? '00:00', 0, 5) }}</p>
                    </div>
                </div>

                {{-- OPEN STATE: QR Display --}}
                <div x-show="!isClosed" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="w-full flex flex-col items-center">
                    <div class="relative p-10 bg-white rounded-[40px] shadow-[0_0_100px_rgba(44,104,245,0.2)]">
                        {{-- QR Container --}}
                        <div class="relative z-10">
                            @if($activeQr)
                                <x-monitor.qr-plinth :qr="$activeQr" />
                            @else
                                <div class="w-[320px] h-[320px] flex items-center justify-center text-slate-200">
                                    <i class="ti ti-loader animate-spin text-5xl"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Countdown Overlay --}}
                        <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 bg-[#0f1e3d] border border-white/20 px-8 py-3 rounded-2xl shadow-2xl z-20 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                <div class="text-[10px] font-black text-white/40 uppercase tracking-widest leading-none">Keamanan<br>Ditinjau</div>
                                <div class="h-8 w-px bg-white/10"></div>
                                <div class="text-2xl font-black font-mono text-[#ffd500] tabular-nums" x-text="renderSeconds()">00</div>
                                <div class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest animate-pulse">Rotating Token</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-20 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Sesi <span x-text="label"></span> Sedang Berlangsung
                        </div>
                        <h2 class="text-3xl font-black font-display text-white">Silakan Arahkan Kamera HP</h2>
                        <p class="mt-2 text-white/40 text-sm font-medium">Buka aplikasi absensi di ponsel Anda untuk memindai kode</p>
                    </div>
                </div>
            </div>

            {{-- Right Side: Real-time Stats & Recent Logs --}}
            <div class="h-full flex flex-col">
                <div class="flex-1 bg-white/5 border border-white/10 rounded-[40px] backdrop-blur-xl p-8 shadow-2xl flex flex-col overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black font-display text-white">Log Kehadiran Terbaru</h3>
                            <p class="text-xs text-white/40 font-bold uppercase tracking-widest mt-1">Real-time Activity Stream</p>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center text-xl text-white">
                            <i class="ti ti-broadcast animate-bounce"></i>
                        </div>
                    </div>

                    {{-- Activity List --}}
                    <div class="flex-1 overflow-y-auto pr-2 space-y-4" id="recentScanList">
                        {{-- Polled dynamically via startPoll --}}
                        <template x-if="scans.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-white/20 py-20">
                                <i class="ti ti-id-badge-off text-6xl mb-4"></i>
                                <p class="text-sm font-bold uppercase tracking-widest">Belum ada pemindaian masuk</p>
                            </div>
                        </template>

                        <template x-for="scan in scans" :key="scan.id">
                            <div class="group bg-white/5 border border-white/5 rounded-3xl p-4 flex items-center gap-4 transition-all hover:bg-white/10 hover:-translate-x-1 duration-300">
                                <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] flex items-center justify-center font-black text-lg text-white shadow-lg">
                                    <span x-text="scan.name.substring(0,1).toUpperCase()"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-black text-white truncate" x-text="scan.name"></p>
                                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-0.5" x-text="scan.identifier + ' · ' + (scan.class || 'Staf')"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-black font-mono text-[#ffd500]" x-text="scan.time"></p>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded-lg border border-emerald-400/20 mt-1 inline-block">BERHASIL</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Summary Panel --}}
                    <div class="mt-8 pt-8 border-t border-white/10 grid grid-cols-2 gap-6">
                        <div class="bg-white/5 p-5 rounded-[28px] border border-white/5">
                            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Kehadiran Hari Ini</p>
                            <div class="flex items-end gap-3">
                                <h4 class="text-4xl font-black font-display text-white" x-text="summary.percentage + '%'">0%</h4>
                                <p class="text-[10px] font-bold text-emerald-400 mb-1" x-text="summary.present + ' Siswa'"></p>
                            </div>
                        </div>
                        <div class="bg-white/5 p-5 rounded-[28px] border border-white/5 flex flex-col justify-center">
                            <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Progress Monitor</p>
                            <div class="w-full bg-white/10 h-2 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-[#2c68f5] to-emerald-400 rounded-full transition-all duration-1000" :style="`width: ${summary.percentage}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

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

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 h-screen flex flex-col py-4">

        {{-- Monitor Header --}}
        <div class="flex items-center justify-between mb-4 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center p-1.5 shadow-lg">
                    <img src="{{ asset('images/logo-smk.png') }}" class="h-full w-full object-contain" alt="Logo">
                </div>
                <div>
                    <h1 class="text-lg font-black font-display tracking-tight text-white uppercase leading-none">{{ $school->name }}</h1>
                    <p class="text-[9px] font-bold text-[#ffd500] uppercase tracking-[0.2em] mt-1">Live Attendance Monitor</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <div class="hidden sm:block bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 backdrop-blur-md">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold font-mono tracking-wider">SYSTEM SECURED</span>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="h-10 px-4 rounded-xl bg-white text-[#0f1e3d] font-black text-[10px] uppercase tracking-wider flex items-center gap-2 hover:bg-[#ffd500] transition-all">
                    <i class="ti ti-layout-dashboard text-base"></i>
                    <span class="hidden xs:inline">Panel Admin</span>
                </a>
            </div>
        </div>

        {{-- Main Monitor Content Area --}}
        <div class="flex-1 grid lg:grid-cols-[45%_55%] gap-4 sm:gap-6 items-center overflow-hidden min-h-0">

            {{-- Left Side: QR Display or Closed State --}}
            <div class="h-full flex items-center justify-center overflow-hidden">

                {{-- CLOSED STATE --}}
                <div x-show="isClosed" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="w-full max-w-md text-center p-6 sm:p-10 rounded-[32px] bg-white/5 border border-white/10 backdrop-blur-xl shadow-2xl">
                    <div class="relative w-20 h-20 mx-auto mb-6">
                        <div class="absolute inset-0 bg-[#ffd500]/20 rounded-full blur-xl animate-pulse"></div>
                        <div class="relative h-full w-full bg-gradient-to-tr from-[#0f1e3d] to-[#1a3a7a] border border-white/20 rounded-full flex items-center justify-center text-3xl text-[#ffd500] shadow-xl">
                            <i class="ti ti-clock-pause"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black font-display text-white leading-tight">Absensi<br>Belum Aktif</h2>
                    <p class="mt-4 text-white/50 text-sm leading-relaxed max-w-xs mx-auto font-medium">
                        Generator QR otomatis akan menyala sesuai jadwal operasional.
                    </p>
                    <div class="mt-8 p-5 rounded-2xl bg-white/5 border border-white/10 w-full">
                        <p class="text-[9px] font-black text-[#ffd500] uppercase tracking-[0.2em] mb-2">Sesi Berikutnya</p>
                        <p class="text-lg font-black font-mono tracking-tight text-white" x-text="label"></p>
                        <div class="h-px w-8 bg-white/20 mx-auto my-2"></div>
                        <p class="text-2xl font-black font-mono text-white tracking-tighter">{{ substr($schedule['start'] ?? '00:00', 0, 5) }} - {{ substr($schedule['end'] ?? '00:00', 0, 5) }}</p>
                    </div>
                </div>

                {{-- OPEN STATE: QR Display --}}
                <div x-show="!isClosed" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="w-full flex flex-col items-center">
                    <div class="relative p-6 sm:p-8 bg-white rounded-[32px] shadow-[0_0_60px_rgba(44,104,245,0.1)]">
                        {{-- QR Container --}}
                        <div class="relative z-10">
                            @if($activeQr)
                                <x-monitor.qr-plinth :qr="$activeQr" />
                            @else
                                <div class="w-[240px] h-[240px] flex items-center justify-center text-slate-200">
                                    <i class="ti ti-loader animate-spin text-4xl"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Countdown Overlay --}}
                        <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-[#0f1e3d] border border-white/20 px-5 py-2 rounded-xl shadow-2xl z-20 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="text-[8px] font-black text-white/40 uppercase tracking-widest leading-tight text-center">Rotating<br>Token</div>
                                <div class="h-6 w-px bg-white/10"></div>
                                <div class="text-lg font-black font-mono text-[#ffd500] tabular-nums" x-text="renderSeconds()">00</div>
                                <div class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 text-center">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase tracking-[0.2em] mb-3">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            Sesi <span x-text="label"></span> Aktif
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black font-display text-white">Silakan Scan Sekarang</h2>
                        <p class="mt-1.5 text-white/40 text-xs font-medium">Buka Menu Scan di aplikasi ponsel Anda</p>
                    </div>
                </div>
            </div>

            {{-- Right Side: Real-time Stats & Recent Logs --}}
            <div class="h-full flex flex-col overflow-hidden">
                <div class="flex-1 bg-white/5 border border-white/10 rounded-[32px] backdrop-blur-xl p-5 sm:p-7 shadow-2xl flex flex-col overflow-hidden min-h-0">
                    <div class="flex items-center justify-between mb-6 flex-shrink-0">
                        <div>
                            <h3 class="text-lg font-black font-display text-white">Aktivitas Terkini</h3>
                            <p class="text-[9px] text-white/30 font-bold uppercase tracking-widest mt-0.5">Real-time Stream</p>
                        </div>
                        <div class="h-8 w-8 rounded-lg bg-white/5 flex items-center justify-center text-white/40">
                            <i class="ti ti-broadcast text-lg"></i>
                        </div>
                    </div>

                    {{-- Activity List --}}
                    <div class="flex-1 overflow-y-auto pr-1 space-y-3 custom-scrollbar" id="recentScanList">
                        <template x-if="scans.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-white/10 py-10">
                                <i class="ti ti-id-badge-2 text-5xl mb-3"></i>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em]">Menunggu Data...</p>
                            </div>
                        </template>

                        <template x-for="scan in scans" :key="scan.id">
                            <div class="group bg-white/5 border border-white/5 rounded-2xl p-3 flex items-center gap-3 transition-all hover:bg-white/10 duration-300">
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] flex items-center justify-center font-black text-sm text-white shadow-lg flex-shrink-0">
                                    <span x-text="scan.name.substring(0,1).toUpperCase()"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-black text-white truncate" x-text="scan.name"></p>
                                    <p class="text-[9px] font-bold text-white/30 uppercase tracking-widest mt-0.5 truncate" x-text="scan.identifier + ' · ' + (scan.class || 'Staf')"></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[10px] font-black font-mono text-[#ffd500]" x-text="scan.time"></p>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-emerald-400 mt-0.5 block">SUCCESS</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Summary Panel --}}
                    <div class="mt-6 pt-6 border-t border-white/10 grid grid-cols-2 gap-4 flex-shrink-0">
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Hadir Hari Ini</p>
                            <div class="flex items-end justify-between">
                                <h4 class="text-2xl font-black font-display text-white leading-none" x-text="summary.percentage + '%'">0%</h4>
                                <p class="text-[9px] font-bold text-emerald-400 mb-0.5" x-text="summary.present + ' Users'"></p>
                            </div>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5 flex flex-col justify-center">
                            <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-2">School Progress</p>
                            <div class="w-full bg-white/10 h-1 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-[#2c68f5] to-emerald-400 rounded-full transition-all duration-1000" :style="`width: ${summary.percentage}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
    @media (max-height: 700px) {
        .h-screen { height: auto; min-height: 100vh; }
    }
</style>

    </div>
@endsection

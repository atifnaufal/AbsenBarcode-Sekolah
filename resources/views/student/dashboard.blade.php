@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-[#f8fafd] text-[#0f172a] font-sans selection:bg-[#2c68f5] selection:text-white" x-data="attendanceDashboard()">

    {{-- Top Dynamic Background --}}
    <div class="fixed top-0 left-0 w-full h-[240px] bg-gradient-to-br from-[#0f1e3d] via-[#1a3a7a] to-[#2c68f5] rounded-b-[40px] shadow-2xl z-0"></div>

    <div class="mx-auto max-w-md px-5 pt-8 pb-32 relative z-10">

        {{-- Header Section --}}
        <header class="flex items-center justify-between mb-8">
            <div class="text-white">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Pusat Absensi Digital</p>
                <h1 class="text-2xl font-black font-display tracking-tight mt-0.5">Hello, {{ str(auth()->user()->name)->explode(' ')->first() }}!</h1>
                <p class="text-xs font-medium opacity-80 mt-0.5">{{ auth()->user()->role?->label() }} · {{ auth()->user()->class_name ?? 'Staf Sekolah' }}</p>
            </div>
            <div class="relative group">
                <div class="h-14 w-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-1 shadow-xl transform transition hover:scale-105">
                    <div class="h-full w-full rounded-xl bg-gradient-to-tr from-[#ffd500] to-[#ff9900] flex items-center justify-center font-display font-black text-xl text-[#0f1e3d]">
                        {{ str(auth()->user()->name)->substr(0,1)->upper() }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Action Hub: Scan QR --}}
        <div class="group relative mb-6">
            <div class="absolute inset-0 bg-gradient-to-r from-[#2c68f5] to-[#623ed8] rounded-[32px] blur-xl opacity-20 transition duration-500 group-hover:opacity-30"></div>
            <div class="relative overflow-hidden rounded-[32px] bg-white p-6 shadow-xl border border-white">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 mb-3">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-black uppercase tracking-wider">Sesi Aktif</span>
                        </div>
                        <h2 class="text-xl font-black font-display text-[#0f1e3d] leading-tight">
                            @if($stats['today']['status'] === 'success')
                                Kehadiran Anda<br>Sudah Tercatat
                            @else
                                Pindai QR Untuk<br>Mulai Absensi
                            @endif
                        </h2>
                        <p class="text-[11px] text-[#64748b] mt-2 font-medium">
                            <i class="ti ti-map-pin-check mr-1 text-[#2c68f5]"></i>
                            {{ now(config('attendance.timezone'))->locale('id')->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                    <div class="h-20 w-20 flex-shrink-0 bg-[#f1f5f9] rounded-3xl flex items-center justify-center text-4xl text-[#2c68f5] shadow-inner">
                        <i class="ti {{ $stats['today']['status'] === 'success' ? 'ti-circle-check-filled text-emerald-500' : 'ti-qrcode' }}"></i>
                    </div>
                </div>

                @if($stats['today']['status'] !== 'success')
                <a href="{{ route('attendance.scan') }}" class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#2c68f5] to-[#1a3a7a] py-4 text-sm font-bold text-white shadow-lg shadow-[#2c68f5]/25 transition hover:shadow-xl active:scale-[0.98]">
                    <i class="ti ti-camera-selfie text-lg"></i>
                    Buka Scanner Sekarang
                </a>
                @else
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="bg-[#f8fafc] rounded-2xl p-3 border border-[#f1f5f9] text-center">
                        <p class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest">Jam Masuk</p>
                        <p class="text-sm font-black text-[#0f1e3d] mt-0.5">{{ $stats['today']['time'] }}</p>
                    </div>
                    <div class="bg-[#f8fafc] rounded-2xl p-3 border border-[#f1f5f9] text-center">
                        <p class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest">Jarak GPS</p>
                        <p class="text-sm font-black text-[#0f1e3d] mt-0.5">{{ $stats['today']['distance'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Statistics & Profile Shortcut --}}
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-3xl p-5 border border-white shadow-lg flex flex-col justify-between">
                <div>
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-3">
                        <i class="ti ti-chart-pie-2"></i>
                    </div>
                    @php $pct = $stats['month']['total'] > 0 ? round(($stats['month']['hadir'] / $stats['month']['total']) * 100) : 0; @endphp
                    <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">Absensi Anda</p>
                    <h3 class="text-2xl font-black text-[#0f1e3d] mt-0.5">{{ $pct }}%</h3>
                </div>
                <div class="w-full bg-[#f1f5f9] h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>

            <a href="{{ route('student.profile') }}" class="bg-white rounded-3xl p-5 border border-white shadow-lg flex flex-col justify-between group">
                <div>
                    <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-3 group-hover:bg-amber-100 transition">
                        <i class="ti ti-user-cog"></i>
                    </div>
                    <p class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-widest">Akun Saya</p>
                    <h3 class="text-sm font-bold text-[#0f1e3d] mt-1 leading-tight">Pengaturan & Profil</h3>
                </div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-amber-600 mt-2">
                    Lengkapi <i class="ti ti-arrow-right"></i>
                </div>
            </a>
        </div>

        {{-- Recent History --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-sm font-black text-[#0f172a] uppercase tracking-widest">Aktivitas Terakhir</h3>
                <span class="text-[10px] font-bold text-[#2c68f5]">Lihat Semua</span>
            </div>

            <div class="space-y-3">
                @forelse($recentScans as $scan)
                <div class="flex items-center gap-4 bg-white p-4 rounded-3xl border border-white shadow-md transition hover:-translate-y-0.5 duration-300">
                    <div class="h-11 w-11 rounded-2xl bg-[#f1f5f9] flex items-center justify-center text-xl flex-shrink-0">
                        @if($scan->result->value === 'success')
                            <i class="ti ti-checkbox text-emerald-500"></i>
                        @else
                            <i class="ti ti-clock-exclamation text-amber-500"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-black text-[#0f1e3d] truncate">{{ $scan->session_label ?? 'Absensi Terhitung' }}</p>
                        <p class="text-[10px] font-medium text-[#64748b] mt-0.5">{{ $scan->attendance_date->translatedFormat('d M Y') }} · {{ $scan->scanned_at?->format('H:i') }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $scan->result->value === 'success' ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $scan->result->value }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 bg-white/50 rounded-3xl border border-dashed border-[#cbd5e1]">
                    <i class="ti ti-folders text-3xl text-[#cbd5e1] mb-2"></i>
                    <p class="text-xs text-[#94a3b8] font-bold">Belum ada riwayat absensi</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Teacher Leaderboard - Only for Guru --}}
        @if((auth()->user()->role === \App\Enums\UserRole::GURU) && isset($teacherRankings) && $teacherRankings->isNotEmpty())
        <div class="mt-8 pt-6 border-t border-[#e2e8f0]">
             <h3 class="text-sm font-black text-[#0f172a] uppercase tracking-widest px-1 mb-4">Peringkat Kedisiplinan Guru</h3>
             <div class="bg-[#0f1e3d] rounded-[32px] p-6 shadow-xl text-white">
                <div class="space-y-4">
                    @foreach($teacherRankings as $index => $teacher)
                    <div class="flex items-center gap-3">
                        <span class="h-6 w-6 rounded-lg bg-white/10 flex items-center justify-center text-[10px] font-black text-[#ffd500]">#{{ $index + 1 }}</span>
                        <p class="flex-1 text-xs font-bold truncate">{{ $teacher->name }}</p>
                        <span class="text-[10px] font-black text-white/50">{{ $teacher->attendances_count }} Hari</span>
                    </div>
                    @endforeach
                </div>
             </div>
        </div>
        @endif

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
    function attendanceDashboard() {
        return {}
    }
</script>
@endsection

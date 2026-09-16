@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-[#f8fafd] text-[#0f172a] font-sans selection:bg-[#2c68f5] selection:text-white pb-32" x-data="attendanceDashboard()">

    {{-- Top Dynamic Background --}}
    <div class="fixed top-0 left-0 w-full h-[220px] bg-gradient-to-br from-[#0f1e3d] via-[#1a3a7a] to-[#2c68f5] rounded-b-[40px] shadow-2xl z-0"></div>

    <div class="mx-auto max-w-md px-5 pt-10 relative z-10">

        {{-- Header Section --}}
        <header class="flex items-center justify-between mb-8">
            <div class="text-white">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Sistem Absensi Digital</p>
                <h1 class="text-2xl font-black font-display tracking-tight mt-0.5">Hi, {{ str(auth()->user()->name)->explode(' ')->first() }}!</h1>
                <p class="text-xs font-medium opacity-80 mt-0.5">{{ auth()->user()->role?->label() }} · {{ auth()->user()->class_name ?? 'Staf Sekolah' }}</p>
            </div>
            <div class="relative">
                <div class="h-12 w-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-1 shadow-xl">
                    <div class="h-full w-full rounded-xl bg-gradient-to-tr from-[#ffd500] to-[#ff9900] flex items-center justify-center font-display font-black text-lg text-[#0f1e3d]">
                        {{ str(auth()->user()->name)->substr(0,1)->upper() }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Action Hub: Scan QR --}}
        <div class="group relative mb-8">
            <div class="absolute inset-0 bg-gradient-to-r from-[#2c68f5] to-[#623ed8] rounded-[32px] blur-xl opacity-20 transition duration-500"></div>
            <div class="relative overflow-hidden rounded-[32px] bg-white p-6 shadow-xl border border-white">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 mb-3">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-black uppercase tracking-wider">Status Absensi</span>
                        </div>
                        <h2 class="text-xl font-black font-display text-[#0f1e3d] leading-tight">
                            @if($stats['today']['status'] === 'success')
                                Kehadiran Telah<br>Tervalidasi!
                            @else
                                Siap Untuk<br>Absensi Hari Ini?
                            @endif
                        </h2>
                    </div>
                    <div class="h-16 w-16 flex-shrink-0 bg-[#f1f5f9] rounded-2xl flex items-center justify-center text-3xl text-[#2c68f5]">
                        <i class="ti {{ $stats['today']['status'] === 'success' ? 'ti-circle-check-filled text-emerald-500' : 'ti-qrcode' }}"></i>
                    </div>
                </div>

                @if($stats['today']['status'] !== 'success')
                <a href="{{ route('attendance.scan') }}" class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#2c68f5] to-[#1a3a7a] py-4 text-sm font-bold text-white shadow-lg shadow-[#2c68f5]/25 transition active:scale-[0.98]">
                    <i class="ti ti-camera-selfie text-lg"></i>
                    Buka Pemindai QR
                </a>
                @else
                <div class="mt-6 flex items-center gap-3">
                    <div class="flex-1 bg-[#f8fafc] rounded-2xl p-3 border border-[#f1f5f9] text-center">
                        <p class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest">Waktu Scan</p>
                        <p class="text-sm font-black text-[#0f1e3d] mt-0.5">{{ $stats['today']['time'] }}</p>
                    </div>
                    <div class="flex-1 bg-[#f8fafc] rounded-2xl p-3 border border-[#f1f5f9] text-center">
                        <p class="text-[9px] font-bold text-[#94a3b8] uppercase tracking-widest">Radius</p>
                        <p class="text-sm font-black text-[#0f1e3d] mt-0.5">{{ $stats['today']['distance'] }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Statistics & Overview --}}
        <div class="space-y-4">
            <h3 class="text-xs font-black text-[#0f172a] uppercase tracking-[0.15em] px-1">Ringkasan Bulan Ini</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-3xl p-5 border border-white shadow-lg">
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-3">
                        <i class="ti ti-chart-bar"></i>
                    </div>
                    @php $pct = $stats['month']['total'] > 0 ? round(($stats['month']['hadir'] / $stats['month']['total']) * 100) : 0; @endphp
                    <p class="text-[9px] font-black text-[#94a3b8] uppercase tracking-widest">Tingkat Hadir</p>
                    <div class="flex items-end justify-between mt-1">
                        <h3 class="text-2xl font-black text-[#0f1e3d]">{{ $pct }}%</h3>
                        <span class="text-[10px] font-bold text-indigo-600 mb-1">{{ $stats['month']['hadir'] }} Hari</span>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-5 border border-white shadow-lg">
                    <div class="h-10 w-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl mb-3">
                        <i class="ti ti-clock-bolt"></i>
                    </div>
                    <p class="text-[9px] font-black text-[#94a3b8] uppercase tracking-widest">Keterlambatan</p>
                    <div class="flex items-end justify-between mt-1">
                        <h3 class="text-2xl font-black text-[#0f1e3d]">{{ $stats['month']['terlambat'] }}</h3>
                        <span class="text-[10px] font-bold text-orange-600 mb-1">Kali</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Logs --}}
        <div class="mt-8 space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-xs font-black text-[#0f172a] uppercase tracking-[0.15em]">Riwayat Terbaru</h3>
                <span class="text-[10px] font-bold text-[#2c68f5] bg-[#2c68f5]/10 px-2 py-0.5 rounded-lg">Real-time</span>
            </div>

            <div class="space-y-3">
                @forelse($recentScans as $scan)
                <div class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-[#f1f5f9] shadow-sm">
                    <div class="h-10 w-10 rounded-xl {{ $scan->result->value === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600' }} flex items-center justify-center text-lg flex-shrink-0">
                        <i class="ti {{ $scan->result->value === 'success' ? 'ti-check' : 'ti-alert-triangle' }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-black text-[#0f1e3d] truncate">{{ $scan->session_label ?? 'Presensi Harian' }}</p>
                        <p class="text-[10px] font-medium text-[#94a3b8] mt-0.5">{{ $scan->attendance_date->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-black text-[#0f1e3d]">{{ $scan->scanned_at?->format('H:i') }}</p>
                        <p class="text-[9px] font-bold uppercase tracking-widest mt-0.5 {{ $scan->result->value === 'success' ? 'text-emerald-500' : 'text-orange-500' }}">
                            {{ $scan->result->value }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 bg-[#f1f5f9]/50 rounded-[32px] border-2 border-dashed border-[#e2e8f0]">
                    <p class="text-[11px] text-[#94a3b8] font-bold uppercase tracking-widest">Belum ada aktivitas</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Leaderboard Guru --}}
        @if((auth()->user()->role === \App\Enums\UserRole::GURU) && isset($teacherRankings) && $teacherRankings->isNotEmpty())
        <div class="mt-8 space-y-4">
             <h3 class="text-xs font-black text-[#0f172a] uppercase tracking-[0.15em] px-1">Peringkat Disiplin Staf</h3>
             <div class="bg-[#0f1e3d] rounded-[32px] p-6 shadow-2xl text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 h-16 w-16 bg-white/5 rounded-full blur-xl"></div>
                <div class="space-y-4 relative z-10">
                    @foreach($teacherRankings as $index => $teacher)
                    <div class="flex items-center gap-3">
                        <span class="h-6 w-6 rounded-lg bg-white/10 flex items-center justify-center text-[10px] font-black {{ $index === 0 ? 'text-[#ffd500]' : 'text-white/40' }}">
                            {{ $index + 1 }}
                        </span>
                        <p class="flex-1 text-[11px] font-bold truncate">{{ $teacher->name }}</p>
                        <span class="text-[10px] font-black text-[#ffd500]/80 bg-[#ffd500]/10 px-2 py-0.5 rounded-md">{{ $teacher->attendances_count }} Hari</span>
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

@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-[1440px] px-2 sm:px-4 lg:px-6">

        {{-- Elite 3D Glassmorphic Header --}}
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-[#0f1e3d] via-[#1a3a7a] to-[#2c68f5] p-6 lg:p-8 text-white shadow-[0_20px_40px_rgba(15,30,61,0.25),inset_0_1px_1px_rgba(255,255,255,0.2)] relative overflow-hidden group">
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl group-hover:scale-110 transition duration-700"></div>
            <div class="absolute -left-12 -bottom-12 h-48 w-48 rounded-full bg-[#623ed8]/20 blur-2xl"></div>

            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="mb-2 inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.25em] text-[#ffd500] bg-white/10 px-3 py-1 rounded-full border border-white/10">
                        <span class="h-2 w-2 rounded-full bg-[#ffd500] animate-pulse"></span>
                        Pusat Kendali Utama ·
                    </div>
                    <h1 class="font-display text-3xl font-black tracking-tight text-white sm:text-4xl">Dashboard Ringkasan Admin</h1>
                    <p class="mt-2 text-sm text-white/80">Selamat datang kembali, <span class="font-bold text-[#ffd500]">{{ $activeUser->name }}</span>. Mengelola aktivitas kehadiran SMK Bina Utama Kendal.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-md px-4 py-2.5 shadow-sm text-white">
                        <div class="text-[9px] font-bold uppercase tracking-widest text-white/60 mb-0.5">Waktu Operasional Server</div>
                        <div class="text-xs font-mono font-bold text-[#ffd500] flex items-center gap-2">
                            <i class="ti ti-clock-play text-lg"></i>
                            {{ $todayLabel }}
                        </div>
                    </div>
                    <a href="{{ route('monitor') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#ffd500] px-6 text-sm font-bold text-[#0f1e3d] shadow-lg shadow-[#ffd500]/20 hover:brightness-105 active:scale-[0.98] transition-all">
                        <i class="ti ti-device-tv-old text-lg"></i>
                        Buka Layar Utama Monitor
                    </a>
                </div>
            </div>
        </div>

        {{-- 3D Stats Cards Grid --}}
        <section class="grid gap-4 grid-cols-2 md:grid-cols-4 lg:gap-6 mb-8" aria-labelledby="stats-heading">
            <h2 id="stats-heading" class="sr-only">Statistik Kehadiran</h2>
            @foreach($stats as $stat)
                @php
                    $colors = [
                        'success' => ['from' => 'from-emerald-500/20', 'text' => 'text-emerald-600', 'border' => 'border-emerald-500/30', 'bg' => 'bg-emerald-500/10'],
                        'warning' => ['from' => 'from-amber-500/20', 'text' => 'text-amber-600', 'border' => 'border-amber-500/30', 'bg' => 'bg-amber-500/10'],
                        'blue' => ['from' => 'from-blue-500/20', 'text' => 'text-blue-600', 'border' => 'border-blue-500/30', 'bg' => 'bg-blue-500/10'],
                        'purple' => ['from' => 'from-purple-500/20', 'text' => 'text-purple-600', 'border' => 'border-purple-500/30', 'bg' => 'bg-purple-500/10'],
                    ];
                    $theme = $colors[$stat['tone']] ?? $colors['blue'];
                @endphp
                <div class="group relative overflow-hidden rounded-3xl border {{ $theme['border'] }} bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.02)] transition-all hover:shadow-[0_15px_35px_rgba(0,0,0,0.06)] hover:-translate-y-1 duration-300">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-gradient-to-br {{ $theme['from'] }} to-transparent opacity-0 transition-opacity group-hover:opacity-100 duration-500"></div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div class="h-11 w-11 rounded-2xl {{ $theme['bg'] }} flex items-center justify-center {{ $theme['text'] }} shadow-inner">
                            <i class="ti {{ $stat['icon'] }} text-xl"></i>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-[#8a95a8] bg-[#f2f5fa] px-2 py-0.5 rounded-md">{{ $stat['label'] }}</span>
                    </div>

                    <div class="relative z-10 mt-5">
                        <div class="font-display text-3xl font-black tracking-tight text-[#0f1e3d] lg:text-4xl">{{ number_format($stat['value']) }}</div>
                        <p class="mt-1 text-xs font-semibold text-[#68748b]">{{ $stat['caption'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Main Dashboard Layout Structure --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Column 1 & 2: Teacher Discipline & Recent Scans --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- TOP: Teacher Discipline & Presence Achievement Analysis --}}
                <div class="rounded-3xl border border-school-line bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-[10px] font-black text-[#623ed8] uppercase tracking-widest bg-[#623ed8]/10 px-2 py-0.5 rounded">Analisis Kedisiplinan Guru</span>
                            <h3 class="font-display text-lg font-bold text-[#0f1e3d] mt-1">Peringkat Guru Terdisiplin Bulan Ini</h3>
                        </div>
                        <i class="ti ti-trophy text-2xl text-amber-500"></i>
                    </div>

                    @if($teacherRankings->isEmpty())
                        <div class="p-6 text-center bg-[#f2f5fa] rounded-2xl border border-dashed border-school-line text-xs text-[#8a95a8]">
                            <i class="ti ti-users text-xl mb-1 block"></i>
                            Belum ada rekaman kehadiran guru bulan ini untuk menyusun peringkat kedisiplinan.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Top Disiplin Guru Card --}}
                            @php $topTeacher = $teacherRankings->first(); @endphp
                            <div class="bg-gradient-to-br from-[#0f1e3d] to-[#1a3a7a] rounded-2xl p-4 text-white border border-white/5 relative overflow-hidden shadow-md">
                                <p class="text-[10px] font-bold text-[#ffd500] uppercase tracking-widest">Peringkat 1 Terdisiplin</p>
                                <div class="flex items-center gap-3 mt-3">
                                    <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center font-bold text-sm text-[#ffd500] border border-white/10">1st</div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold truncate">{{ $topTeacher->name }}</p>
                                        <p class="text-[10px] text-white/60">Rerata Datang: <span class="font-bold text-[#ffd500]">{{ $topTeacher->formatted_avg_time }} WIB</span></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Guru Summary Performance --}}
                            <div class="bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 rounded-2xl p-4 border border-emerald-500/20 flex flex-col justify-center">
                                <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest">Kriteria Penilaian</p>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-2xl font-black text-emerald-700">Waktu Datang</span>
                                    <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Terawal adalah pemenang</span>
                                </div>
                            </div>
                        </div>

                        {{-- Real Ranking Table for high aesthetics --}}
                        <div class="mt-4 overflow-hidden rounded-xl border border-school-line text-xs">
                            <div class="bg-[#f2f5fa] p-2.5 font-bold text-[#0f1e3d] grid grid-cols-4">
                                <span class="col-span-2">Nama Guru / Staf</span>
                                <span class="text-center">Hadir Bulan Ini</span>
                                <span class="text-right">Rerata Jam Datang</span>
                            </div>
                            <div class="divide-y divide-school-line">
                                @foreach($teacherRankings as $index => $teacher)
                                <div class="p-2.5 grid grid-cols-4 items-center">
                                    <span class="col-span-2 font-semibold text-[#172033] flex items-center gap-2">
                                        <span class="font-mono text-[10px] text-school-muted">#{{ $index + 1 }}</span>
                                        <span class="truncate">{{ $teacher->name }}</span>
                                    </span>
                                    <span class="text-center font-mono font-bold">{{ $teacher->attendances_count }} Hari</span>
                                    <span class="text-right font-bold text-emerald-600">{{ $teacher->formatted_avg_time }} WIB</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Recent Pemindaian Table --}}
                <div class="rounded-3xl border border-school-line bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-school-line bg-white p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="font-display text-lg font-bold text-[#0f1e3d]">Pemindaian Absensi Terkini</h3>
                            <p class="text-xs text-[#8a95a8] mt-1">Aktivitas penyerapan log scan</p>
                        </div>
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex h-9 items-center justify-center px-4 rounded-xl border border-[#623ed8] text-xs font-bold text-[#623ed8] hover:bg-[#623ed8]/5 transition">
                            Lihat Semua Laporan
                        </a>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <x-dashboard.recent-scans :scans="$recentScans" :school="$school" />
                    </div>
                </div>
            </div>

            {{-- Column 3: Quick Insights, Configurations & Profile Info --}}
            <div class="space-y-6">

                {{-- Dynamic Validation Status Panel --}}
                <div class="rounded-3xl border border-transparent bg-gradient-to-b from-[#0f1e3d] to-[#111a31] p-6 text-white shadow-xl shadow-[#0f1e3d]/10 relative overflow-hidden group">
                    <div class="absolute -right-12 -bottom-12 h-32 w-32 rounded-full bg-[#2c68f5]/10 blur-xl"></div>

                    <h3 class="font-display text-lg font-bold mb-4 flex items-center justify-between">
                        <span>Status Sistem Validasi</span>
                        <span class="h-2 w-2 rounded-full bg-green-400 animate-ping"></span>
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 bg-white/5 p-3 rounded-2xl border border-white/10">
                            <div class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center text-green-400">
                                <i class="ti ti-map-2 text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[9px] uppercase font-bold text-white/40 tracking-wider">Radius Peta Lokasi</div>
                                <div class="text-xs font-bold text-white truncate">{{ $school->radius_meters }} Meter (Radius Aktif)</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/5 p-3 rounded-2xl border border-white/10">
                            <div class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center text-blue-400">
                                <i class="ti ti-refresh-dot text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[9px] uppercase font-bold text-white/40 tracking-wider">QR Token Code</div>
                                <div class="text-xs font-bold text-white truncate">Dynamic Auto-Rotate (Secured)</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/5 p-3 rounded-2xl border border-white/10">
                            <div class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center text-purple-400">
                                <i class="ti ti-cloud-computing text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[9px] uppercase font-bold text-white/40 tracking-wider">Koneksi Database</div>
                                <div class="text-xs font-bold text-white truncate">{{ config('firebase_integration.enabled', true) ? 'Firebase Connected' : 'Local Standalone' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-white/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-white/60">Persentase Kehadiran Sekolah Hari Ini</span>
                            <span class="text-xs font-black text-[#ffd500] font-mono">{{ $summary['percentage'] }}%</span>
                        </div>
                        <div class="h-2.5 w-full bg-white/10 rounded-full overflow-hidden p-0.5 border border-white/5">
                            <div class="h-full bg-gradient-to-r from-blue-400 via-indigo-400 to-green-400 rounded-full transition-all duration-500 shadow-[0_0_8px_rgba(52,211,153,0.5)]" style="width: {{ $summary['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- School Profile Information Card Container --}}
                <div class="rounded-3xl border border-school-line bg-white p-6 shadow-sm space-y-4">
                    <h3 class="font-display text-base font-bold text-[#0f1e3d]">Profil Instansi</h3>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-school-canvas border border-school-line">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-tr from-[#0f1e3d] to-[#2c68f5] flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                            SMK
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-bold text-[#0f1e3d] truncate">{{ $school->name }}</div>
                            <div class="text-[10px] text-[#8a95a8] truncate">{{ $school->address }}</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.location.edit') }}" class="flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-school-line text-xs font-bold text-[#0f1e3d] hover:bg-school-canvas transition-all">
                        <i class="ti ti-map-pin-cog"></i>
                        Kelola Parameter Lokasi & Peta Maps
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer Branding --}}
        <footer class="mt-12 mb-8 flex flex-col items-center justify-between gap-4 border-t border-school-line pt-8 text-[10px] font-bold uppercase tracking-widest text-[#9aa4b5] sm:flex-row">
            <div class="flex items-center gap-4">
                <span>© 2026 {{ $school->name }}</span>
                <span class="h-1 w-1 rounded-full bg-[#9aa4b5]"></span>
                <span>Absensi Digital Engine V2.0</span>
            </div>
            <div class="flex items-center gap-2 bg-emerald-500/10 px-3 py-1 rounded-full text-emerald-700 border border-emerald-500/20">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Operasional Berjalan Lancar & Responsif
            </div>
        </footer>
    </div>
@endsection

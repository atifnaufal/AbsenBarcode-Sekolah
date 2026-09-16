@extends('layouts.app')
@section('content')
<div class="max-w-[1280px] mx-auto px-2 sm:px-4" x-data="{ downloading: false, downloadType: '' }">

    {{-- Download Animation Overlay --}}
    <div x-show="downloading" x-transition class="fixed inset-0 z-50 bg-[#0f1e3d]/80 backdrop-blur-md flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl border border-white/20 transform scale-100 transition-all">
            <div class="relative w-20 h-20 mx-auto mb-4 flex items-center justify-center rounded-2xl bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] text-white text-3xl shadow-xl shadow-[#2c68f5]/20">
                <i class="ti ti-download-circle animate-bounce"></i>
                <div class="absolute inset-0 rounded-2xl border-4 border-white/30 border-t-white animate-spin"></div>
            </div>
            <h3 class="text-base font-bold text-[#0f1e3d] capitalize" x-text="'Mengekspor Dokumen ' + downloadType"></h3>
            <p class="text-xs text-[#8a95a8] mt-1 leading-relaxed">
                Sistem sedang mengompilasi lembar rekap data kehadiran, menyusun struktur data tabular, dan meluncurkan berkas unduhan. Mohon tunggu...
            </p>
            <div class="w-full bg-[#f2f5fa] h-2 rounded-full mt-4 overflow-hidden p-0.5 border border-school-line">
                <div class="h-full bg-gradient-to-r from-[#2c68f5] to-[#623ed8] rounded-full animate-[pulse_1.5s_infinite]" style="width: 75%"></div>
            </div>
        </div>
    </div>

    {{-- Title Header --}}
    <div class="mb-6">
        <div class="text-[10px] font-bold uppercase tracking-wider text-[#623ed8] mb-1">Pusat Arsip Digital</div>
        <h1 class="text-2xl font-black text-[#0f1e3d] font-display">Laporan Riwayat Kehadiran</h1>
    </div>

    {{-- Search & Export Box --}}
    <div class="bg-white rounded-2xl border border-school-line p-4 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Filter Form --}}
            <form class="flex flex-wrap items-center gap-3 flex-1">
                <div class="flex items-center gap-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe Filter:</label>
                    <select name="filter_type" class="h-10 pl-3 pr-8 rounded-xl border border-school-line text-xs font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:outline-none transition bg-slate-50">
                        <option value="day" {{ $filterType === 'day' ? 'selected' : '' }}>Harian</option>
                        <option value="month" {{ $filterType === 'month' ? 'selected' : '' }}>Bulanan</option>
                        <option value="year" {{ $filterType === 'year' ? 'selected' : '' }}>Tahunan</option>
                        <option value="semester" {{ $filterType === 'semester' ? 'selected' : '' }}>Semester</option>
                    </select>
                </div>

                <div class="relative">
                    <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-base text-[#8a95a8]"></i>
                    <input type="date" name="date" value="{{ $date }}" class="h-10 pl-9 pr-3 rounded-xl border border-school-line text-xs font-semibold text-[#172033] focus:border-[#2c68f5] focus:outline-none transition">
                </div>

                <button class="h-10 px-4 rounded-xl bg-[#0f1e3d] text-xs font-bold text-white hover:bg-[#1a2d52] transition">
                    Terapkan Filter
                </button>
            </form>

            {{-- Advanced Multi Export Action Buttons Suite --}}
            <div class="flex flex-wrap items-center gap-2">
                @php $exportParams = ['date' => $date, 'filter_type' => $filterType]; @endphp
                {{-- CSV Export Button --}}
                <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'csv'])) }}"
                   @click="downloading = true; downloadType = 'CSV'; setTimeout(() => downloading = false, 2500)"
                   class="h-10 px-4 rounded-xl bg-slate-100 border border-school-line text-xs font-bold text-slate-700 hover:bg-slate-200 transition flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-file-text text-sm text-slate-500"></i>
                    Export CSV
                </a>

                {{-- Excel Export Button Sim --}}
                <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'excel'])) }}"
                   @click="downloading = true; downloadType = 'Excel'; setTimeout(() => downloading = false, 2500)"
                   class="h-10 px-4 rounded-xl bg-emerald border border-emerald-200 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-file-spreadsheet text-sm text-emerald-600"></i>
                    Export Excel
                </a>

                {{-- PDF Export Button Sim --}}
                <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'pdf'])) }}"
                   @click="downloading = true; downloadType = 'PDF'; setTimeout(() => downloading = false, 2500)"
                   class="h-10 px-4 rounded-xl bg-red-50 border border-red-200 text-xs font-bold text-red-700 hover:bg-red-100 transition flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-file-type-pdf text-sm text-red-600"></i>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Data Display Panel --}}
    <div class="bg-white rounded-3xl border border-school-line shadow-sm overflow-hidden">
        @if($attendances->isEmpty())
            <div class="p-12 text-center max-w-sm mx-auto flex flex-col items-center justify-center">
                <div class="text-4xl text-[#8a95a8] mb-3">📋</div>
                <h3 class="text-base font-bold text-[#0f1e3d]">Arsip Kosong</h3>
                <p class="text-xs text-[#8a95a8] mt-1 leading-relaxed">
                    Tidak terdeteksi adanya rekaman pemindaian absensi digital siswa maupun guru pada tanggal yang dipilih.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#f8f9fc] text-xs font-bold uppercase tracking-wider text-[#68748b] border-b border-school-line">
                        <tr>
                            <th class="px-6 py-4 text-left">Identitas User</th>
                            <th class="px-6 py-4 text-left">Grup / Kelas</th>
                            <th class="px-6 py-4 text-left">Keterangan</th>
                            <th class="px-6 py-4 text-center">Waktu Scan</th>
                            <th class="px-6 py-4 text-right">Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-school-line text-[#172033]">
                        @foreach($attendances as $a)
                        <tr class="hover:bg-[#f8f9fc]/50 transition duration-150">
                            <td class="px-6 py-4 font-semibold text-[#0f1e3d] flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-[#2c68f5]/10 flex items-center justify-center text-xs font-bold text-[#2c68f5]">
                                    {{ str($a->user?->name ?? '')->substr(0,1)->upper() }}
                                </div>
                                <div>
                                    <p class="font-bold text-[#0f1e3d]">{{ $a->user?->name ?? '-' }}</p>
                                    <p class="text-[10px] text-[#8a95a8] font-mono mt-0.5">{{ $a->user?->identifier ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[#68748b] font-medium">{{ $a->user?->class_name ?? 'Staf / Guru' }}</td>
                            <td class="px-6 py-4 text-[#68748b] text-xs font-bold">{{ $a->session_label ?? '-' }}</td>
                            <td class="px-6 py-4 text-center font-mono font-bold text-xs text-[#0f1e3d]">{{ $a->scanned_at?->format('H:i:s') ?? '--:--' }}</td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $isSuccess = $a->result->value === 'success';
                                    $isLate = $a->result->value === 'late';
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{
                                    $isSuccess ? 'bg-emerald-100 text-emerald-700' : ($isLate ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')
                                }}">
                                    {{ ucfirst($a->result->value) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="p-4 border-t border-school-line bg-[#f8f9fc]">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

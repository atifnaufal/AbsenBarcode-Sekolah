@extends('layouts.app')
@section('content')
<div class="max-w-[1280px] mx-auto px-2 sm:px-4" x-data="{ downloading: false, downloadType: '', editingId: null, labelValue: '' }">

    {{-- Download Animation Overlay --}}
    <div x-show="downloading" x-transition class="fixed inset-0 z-[100] bg-[#0f1e3d]/80 backdrop-blur-md flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-[40px] p-10 max-w-sm w-full text-center shadow-2xl border border-white/20">
            <div class="relative w-20 h-20 mx-auto mb-6 flex items-center justify-center rounded-3xl bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] text-white text-3xl shadow-xl shadow-[#2c68f5]/20">
                <i class="ti ti-download-circle animate-bounce"></i>
                <div class="absolute inset-0 rounded-3xl border-4 border-white/30 border-t-white animate-spin"></div>
            </div>
            <h3 class="text-lg font-black text-[#0f1e3d] capitalize" x-text="'Ekspor ' + downloadType"></h3>
            <p class="text-xs text-[#8a95a8] mt-2 leading-relaxed font-medium">
                Sistem sedang mengompilasi data kehadiran dan menyusun dokumen laporan Anda...
            </p>
        </div>
    </div>

    {{-- Title Header --}}
    <div class="mb-8">
        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-[#623ed8] mb-1.5">Arsip Kehadiran Digital</div>
        <h1 class="text-3xl font-black text-[#0f1e3d] font-display">Laporan & Audit Presensi</h1>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-school-line flex items-center gap-5">
            <div class="h-14 w-14 rounded-2xl bg-blue-50 text-[#2c68f5] flex items-center justify-center text-2xl shadow-inner">
                <i class="ti ti-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Anggota</p>
                <h3 class="text-2xl font-black text-[#0f1e3d]">{{ $usersCount }} <span class="text-xs text-slate-400 font-bold">User</span></h3>
            </div>
        </div>
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-school-line flex items-center gap-5">
            <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-inner">
                <i class="ti ti-user-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hadir (Filter)</p>
                <h3 class="text-2xl font-black text-[#0f1e3d]">{{ $presentCount }} <span class="text-xs text-slate-400 font-bold">User</span></h3>
            </div>
        </div>
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-school-line flex items-center gap-5">
            <div class="h-14 w-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-2xl shadow-inner">
                <i class="ti ti-user-x"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tidak Hadir</p>
                <h3 class="text-2xl font-black text-[#0f1e3d]">{{ $usersCount - $presentCount }} <span class="text-xs text-slate-400 font-bold">User</span></h3>
            </div>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="bg-white rounded-[32px] border border-school-line p-6 shadow-sm mb-8">
        <form class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px] space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Pilih Grup / Kelas</label>
                <select name="class_name" class="w-full h-12 pl-4 pr-10 rounded-2xl border border-school-line text-sm font-bold text-[#0f1e3d] appearance-none focus:border-[#2c68f5] outline-none transition bg-slate-50 shadow-inner">
                    <option value="">Semua Grup</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ $className === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Peran</label>
                <select name="role" class="h-12 px-4 rounded-2xl border border-school-line text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none bg-slate-50 shadow-inner">
                    <option value="">Semua</option>
                    <option value="siswa" {{ $role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru" {{ $role === 'guru' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Rentang</label>
                <select name="filter_type" class="h-12 px-4 rounded-2xl border border-school-line text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none bg-slate-50 shadow-inner">
                    <option value="day" {{ $filterType === 'day' ? 'selected' : '' }}>Harian</option>
                    <option value="month" {{ $filterType === 'month' ? 'selected' : '' }}>Bulanan</option>
                    <option value="year" {{ $filterType === 'year' ? 'selected' : '' }}>Tahunan</option>
                    <option value="semester" {{ $filterType === 'semester' ? 'selected' : '' }}>Semester</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Tanggal</label>
                <input type="date" name="date" value="{{ $date }}" class="h-12 px-4 rounded-2xl border border-school-line text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none bg-slate-50 shadow-inner">
            </div>

            <button class="h-12 px-8 rounded-2xl bg-[#0f1e3d] text-white text-xs font-black uppercase tracking-widest hover:bg-[#1a2d52] transition shadow-lg">
                Filter Data
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-50 flex justify-end gap-3">
             @php $exportParams = ['date' => $date, 'filter_type' => $filterType, 'role' => $role, 'class_name' => $className]; @endphp
             <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'csv'])) }}" @click="downloading = true; downloadType = 'CSV'; setTimeout(() => downloading = false, 3000)" class="h-11 px-5 rounded-xl bg-slate-50 border border-school-line text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-slate-100 transition">
                <i class="ti ti-file-text text-base text-slate-400"></i> CSV
             </a>
             <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'excel'])) }}" @click="downloading = true; downloadType = 'EXCEL'; setTimeout(() => downloading = false, 3000)" class="h-11 px-5 rounded-xl bg-emerald-50 border border-emerald-100 text-[10px] font-black uppercase tracking-widest text-emerald-700 flex items-center gap-2 hover:bg-emerald-100 transition">
                <i class="ti ti-file-spreadsheet text-base"></i> EXCEL
             </a>
             <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'pdf'])) }}" @click="downloading = true; downloadType = 'PDF'; setTimeout(() => downloading = false, 3000)" class="h-11 px-5 rounded-xl bg-red-50 border border-red-100 text-[10px] font-black uppercase tracking-widest text-red-700 flex items-center gap-2 hover:bg-red-100 transition">
                <i class="ti ti-file-type-pdf text-base"></i> PDF
             </a>
        </div>
    </div>

    {{-- Main Table Panel --}}
    <div class="bg-white rounded-[40px] border border-school-line shadow-sm overflow-hidden mb-12">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#f8f9fc] text-[10px] font-black uppercase tracking-[0.15em] text-[#68748b] border-b border-school-line">
                    <tr>
                        <th class="px-8 py-5 text-left">User Identity</th>
                        <th class="px-8 py-5 text-left">Group / Class</th>
                        <th class="px-8 py-5 text-left">Agenda / Notes</th>
                        <th class="px-8 py-5 text-center">Scan Time</th>
                        <th class="px-8 py-5 text-right">Status</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-school-line text-[#172033]">
                    @foreach($users as $u)
                    @php $a = $u->attendances->first(); @endphp
                    <tr class="hover:bg-[#f8f9fc]/80 transition group {{ !$a ? 'bg-red-50/20' : '' }}">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-11 w-11 rounded-2xl bg-white border border-school-line shadow-sm overflow-hidden flex items-center justify-center text-sm font-black text-[#2c68f5]">
                                    @if($u->avatar_url) <img src="{{ $u->avatar_url }}" class="h-full w-full object-cover"> @else {{ str($u->name)->substr(0,1)->upper() }} @endif
                                </div>
                                <div>
                                    <p class="font-black text-[#0f1e3d] text-sm">{{ $u->name }}</p>
                                    <p class="text-[10px] text-[#8a95a8] font-bold uppercase tracking-tighter">{{ $u->identifier }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-[#0f1e3d] text-[10px] font-black border border-slate-200 uppercase">
                                {{ $u->class_name ?? 'STAF' }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            @if($a)
                                <div x-show="editingId !== {{ $a->id }}" class="flex items-center gap-2 group/edit">
                                    <span class="text-xs font-bold text-[#68748b]">{{ $a->session_label ?: '—' }}</span>
                                    <button @click="editingId = {{ $a->id }}; labelValue = '{{ $a->session_label }}'" class="opacity-0 group-hover/edit:opacity-100 transition text-[#2c68f5]">
                                        <i class="ti ti-edit text-base"></i>
                                    </button>
                                </div>
                                <div x-show="editingId === {{ $a->id }}" class="flex items-center gap-2">
                                    <form action="{{ route('attendances.update-keterangan', $a) }}" method="POST" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <input type="text" name="session_label" x-model="labelValue" class="h-9 w-40 bg-white border-2 border-[#2c68f5] rounded-xl px-3 text-xs font-bold outline-none shadow-lg">
                                        <button type="submit" class="h-9 px-3 bg-[#2c68f5] text-white rounded-xl text-[10px] font-black uppercase">Simpan</button>
                                        <button type="button" @click="editingId = null" class="h-9 px-3 bg-slate-100 text-slate-400 rounded-xl text-[10px] font-black uppercase">X</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-300 font-bold uppercase tracking-widest italic">Tanpa Rekaman</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="text-xs font-black font-mono text-[#0f1e3d] {{ !$a ? 'opacity-20' : '' }}">{{ $a && $a->scanned_at ? $a->scanned_at->format('H:i:s') : '--:--:--' }}</span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($a)
                                <span class="inline-flex px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $a->result->value === 'success' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-orange-50 text-orange-600 border border-orange-100' }}">
                                    {{ $a->result->value }}
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 rounded-xl bg-red-50 text-red-600 border border-red-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                    TIDAK HADIR
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($a)
                                <form action="{{ route('admin.attendances.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus permanen log ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="h-9 w-9 rounded-xl bg-white text-red-500 border border-red-100 hover:bg-red-500 hover:text-white transition shadow-sm active:scale-90 flex items-center justify-center">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] font-black text-slate-300 uppercase opacity-30">Lock</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-school-line bg-[#f8f9fc]">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

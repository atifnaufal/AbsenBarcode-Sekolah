@extends('layouts.app')
@section('content')
<div class="max-w-[1440px] mx-auto px-4 py-8" x-data="{ downloading: false, downloadType: '', editingId: null, labelValue: '' }">

    {{-- Download Animation Overlay --}}
    <div x-show="downloading" x-transition.opacity class="fixed inset-0 z-[100] bg-[#0f1e3d]/80 backdrop-blur-md flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-[40px] p-10 max-w-sm w-full text-center shadow-2xl border border-white/20">
            <div class="relative w-20 h-20 mx-auto mb-6 flex items-center justify-center rounded-3xl bg-gradient-to-tr from-[#2c68f5] to-[#623ed8] text-white text-3xl shadow-xl shadow-[#2c68f5]/20">
                <i class="ti ti-download-circle animate-bounce"></i>
                <div class="absolute inset-0 rounded-3xl border-4 border-white/30 border-t-white animate-spin"></div>
            </div>
            <h3 class="text-lg font-black text-[#0f1e3d] capitalize" x-text="'Ekspor ' + downloadType"></h3>
            <p class="text-xs text-[#8a95a8] mt-2 leading-relaxed font-medium uppercase tracking-widest">Memproses Dokumen...</p>
        </div>
    </div>

    {{-- Title Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-[0.15em] mb-3 border border-emerald-100/50">
                <i class="ti ti-history text-xs"></i>
                Pusat Arsip Digital
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-[#0f1e3d] font-display tracking-tight">Audit Kehadiran</h1>
            <p class="text-sm text-[#64748b] mt-2 font-medium">Rekapitulasi aktivitas harian, bulanan, hingga semesteran siswa dan guru.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
             @php $exportParams = ['date' => $date, 'filter_type' => $filterType, 'role' => $role, 'class_name' => $className]; @endphp
             <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'excel'])) }}" @click="downloading = true; downloadType = 'EXCEL'; setTimeout(() => downloading = false, 3000)" class="h-12 px-6 rounded-2xl bg-white border border-school-line text-xs font-black uppercase tracking-widest text-emerald-600 flex items-center gap-2 hover:bg-emerald-50 hover:border-emerald-100 transition-all shadow-sm">
                <i class="ti ti-file-spreadsheet text-lg"></i> EXCEL
             </a>
             <a href="{{ route('admin.reports.export', array_merge($exportParams, ['format' => 'pdf'])) }}" @click="downloading = true; downloadType = 'PDF'; setTimeout(() => downloading = false, 3000)" class="h-12 px-6 rounded-2xl bg-[#0f1e3d] text-white text-xs font-black uppercase tracking-widest flex items-center gap-2 hover:bg-[#1a2d52] transition-all shadow-xl shadow-[#0f1e3d]/15">
                <i class="ti ti-printer text-lg"></i> Cetak PDF
             </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-[32px] p-6 border border-school-line shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500"><i class="ti ti-users text-8xl text-blue-600"></i></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Target</p>
            <h3 class="text-3xl font-black text-[#0f1e3d]">{{ $usersCount }} <span class="text-xs text-slate-400 font-bold uppercase ml-1">Jiwa</span></h3>
        </div>
        <div class="bg-white rounded-[32px] p-6 border border-school-line shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500"><i class="ti ti-user-check text-8xl text-emerald-600"></i></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Hadir Sukses</p>
            <h3 class="text-3xl font-black text-emerald-600">{{ $presentCount }} <span class="text-xs text-slate-400 font-bold uppercase ml-1">Jiwa</span></h3>
        </div>
        <div class="bg-white rounded-[32px] p-6 border border-school-line shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500"><i class="ti ti-user-x text-8xl text-red-600"></i></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tidak Hadir / Mangkir</p>
            <h3 class="text-3xl font-black text-red-500">{{ $usersCount - $presentCount }} <span class="text-xs text-slate-400 font-bold uppercase ml-1">Jiwa</span></h3>
        </div>
        <div class="bg-white rounded-[32px] p-6 border border-school-line shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500"><i class="ti ti-chart-pie text-8xl text-purple-600"></i></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Rasio Kehadiran</p>
            @php $pct = $usersCount > 0 ? round(($presentCount / $usersCount) * 100) : 0; @endphp
            <h3 class="text-3xl font-black text-purple-600">{{ $pct }}%</h3>
        </div>
    </div>

    {{-- Filter Console --}}
    <div class="bg-white rounded-[32px] border border-school-line p-8 shadow-sm mb-10">
        <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Peran User</label>
                <select name="role" class="w-full h-12 px-4 rounded-2xl border border-school-line text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none bg-slate-50 transition-all">
                    <option value="siswa" {{ $role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru" {{ $role === 'guru' ? 'selected' : '' }}>Guru / Staf</option>
                </select>
            </div>

            <div class="space-y-2" x-show="'{{ $role }}' === 'siswa'">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Pilih Kelas</label>
                <select name="class_name" class="w-full h-12 px-4 rounded-2xl border border-school-line text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none bg-slate-50 transition-all">
                    <option value="">Seluruh Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ $className === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Tipe Laporan</label>
                <select name="filter_type" class="w-full h-12 px-4 rounded-2xl border border-school-line text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none bg-slate-50 transition-all">
                    <option value="day" {{ $filterType === 'day' ? 'selected' : '' }}>Harian</option>
                    <option value="month" {{ $filterType === 'month' ? 'selected' : '' }}>Bulanan</option>
                    <option value="year" {{ $filterType === 'year' ? 'selected' : '' }}>Tahunan</option>
                    <option value="semester" {{ $filterType === 'semester' ? 'selected' : '' }}>Semester</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Pilih Waktu</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full h-12 px-4 rounded-2xl border border-school-line text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none bg-slate-50 transition-all">
            </div>

            <button class="h-12 w-full rounded-2xl bg-[#0f1e3d] text-white text-xs font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">
                Update Laporan
            </button>
        </form>
    </div>

    {{-- Main Data Panel --}}
    <div class="bg-white rounded-[40px] border border-school-line shadow-xl shadow-slate-200/50 overflow-hidden" x-data="{ manualId: null, manualName: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#f8f9fc] border-b border-school-line">
                        <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Anggota</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Grup / Kelas</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Catatan Agenda</th>
                        <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Waktu Scan</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Status</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-school-line">
                    @foreach($users as $u)
                    @php $a = $u->attendances->first(); @endphp
                    <tr class="hover:bg-slate-50/50 transition duration-150 {{ !$a ? 'bg-red-50/10' : '' }}">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-11 w-11 rounded-2xl bg-white border border-school-line shadow-sm overflow-hidden flex items-center justify-center text-sm font-black text-[#2c68f5] flex-shrink-0">
                                    @if($u->avatar_url) <img src="{{ $u->avatar_url }}" class="h-full w-full object-cover"> @else {{ str($u->name)->substr(0,1)->upper() }} @endif
                                </div>
                                <div>
                                    <p class="font-black text-[#0f1e3d] text-sm">{{ $u->name }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">{{ $u->identifier }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-[#0f1e3d] text-[10px] font-black border border-slate-200 uppercase tracking-tighter whitespace-nowrap">
                                {{ $u->class_name ?: 'STAF' }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            @if($a)
                                <div x-show="editingId !== {{ $a->id }}" class="flex items-center gap-2 group/edit">
                                    <span class="text-xs font-bold text-[#68748b] italic">{{ $a->session_label ?: '—' }}</span>
                                    <button @click="editingId = {{ $a->id }}; labelValue = '{{ $a->session_label }}'" class="opacity-0 group-hover/edit:opacity-100 transition text-[#2c68f5] hover:scale-110">
                                        <i class="ti ti-edit text-base"></i>
                                    </button>
                                </div>
                                <div x-show="editingId === {{ $a->id }}" class="flex items-center gap-2" x-cloak>
                                    <form action="{{ route('admin.attendances.update-keterangan', $a) }}" method="POST" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <input type="text" name="session_label" x-model="labelValue" class="h-9 w-40 bg-white border-2 border-[#2c68f5] rounded-xl px-3 text-xs font-bold outline-none shadow-lg">
                                        <button type="submit" class="h-9 px-3 bg-[#2c68f5] text-white rounded-xl text-[10px] font-black uppercase">Save</button>
                                        <button type="button" @click="editingId = null" class="h-9 px-3 bg-slate-100 text-slate-400 rounded-xl text-[10px] font-black uppercase">X</button>
                                    </form>
                                </div>
                            @else
                                <button @click="manualId = {{ $u->id }}; manualName = '{{ addslashes($u->name) }}'; $nextTick(() => $refs.manualDialog.showModal())" class="h-8 px-4 rounded-xl border-2 border-dashed border-slate-200 text-[10px] text-slate-400 font-black uppercase tracking-widest hover:border-[#2c68f5] hover:text-[#2c68f5] transition-all">+ Catat Izin</button>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-center font-mono font-black text-xs text-[#0f1e3d] {{ !$a ? 'opacity-20' : '' }}">
                            {{ $a && $a->scanned_at ? $a->scanned_at->format('H:i:s') : '--:--:--' }}
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($a)
                                @php
                                    $res = $a->result->value;
                                    $badge = match($res) {
                                        'success' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'permission' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        'sick' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'absent' => 'bg-red-50 text-red-600 border-red-100',
                                        default => 'bg-orange-50 text-orange-600 border-orange-100',
                                    };
                                @endphp
                                <span class="inline-flex px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest border {{ $badge }}">
                                    {{ $a->result->label() }}
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 rounded-xl bg-red-50 text-red-600 border border-red-100 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                    TIDAK HADIR
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                @if($a)
                                    <form action="{{ route('admin.attendances.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus permanen log ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="h-9 w-9 rounded-xl bg-red-50 text-red-500 border border-red-100 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center active:scale-90">
                                            <i class="ti ti-trash text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Manual Entry Dialog --}}
        <dialog x-ref="manualDialog" class="modal rounded-[40px] p-0 shadow-2xl border-none overflow-hidden max-w-sm w-full backdrop:bg-black/60 backdrop:backdrop-blur-sm">
            <div class="bg-gradient-to-br from-[#0f1e3d] to-[#1a3a7a] p-8 text-white">
                <h3 class="font-black font-display uppercase tracking-[0.15em] text-lg">Pencatatan Manual</h3>
                <p class="text-[10px] font-bold text-white/60 mt-1 uppercase tracking-widest" x-text="manualName"></p>
            </div>
            <form method="POST" action="{{ route('admin.attendances.store-manual') }}" class="p-8 space-y-6 bg-white">
                @csrf
                <input type="hidden" name="user_id" :value="manualId">
                <input type="hidden" name="attendance_date" value="{{ $date }}">

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 block">Status Kehadiran</label>
                    <select name="result" required class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] outline-none focus:border-[#2c68f5] transition-all">
                        <option value="success">Hadir (Manual)</option>
                        <option value="permission">Izin</option>
                        <option value="sick">Sakit</option>
                        <option value="absent">Tidak Hadir (Alfa)</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 block">Alasan / Keterangan</label>
                    <input type="text" name="session_label" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] outline-none focus:border-[#2c68f5] transition-all" placeholder="Contoh: Izin Perlombaan">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="submit" class="flex-1 h-12 bg-[#2c68f5] text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all">Simpan</button>
                    <button type="button" @click="$refs.manualDialog.close()" class="flex-1 h-12 bg-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl active:scale-95 transition-all">Batal</button>
                </div>
            </form>
        </dialog>

        <div class="px-8 py-6 border-t border-school-line bg-[#f8f9fc] flex flex-col sm:flex-row items-center justify-between gap-4">
             <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Audit Anggota: {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }}</p>
             <div class="pagination-custom">
                {{ $users->links() }}
             </div>
        </div>
    </div>
</div>
@endsection

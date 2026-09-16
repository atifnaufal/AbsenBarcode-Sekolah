@extends('layouts.app')
@section('content')
<div class="max-w-[1280px] mx-auto px-2 sm:px-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-[#623ed8] mb-1">Manajemen Waktu</div>
            <h1 class="text-2xl font-black text-[#0f1e3d] font-display">Agenda & Notifikasi Jam</h1>
        </div>
        <button onclick="document.getElementById('addScheduleModal').showModal()" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#2c68f5] to-[#623ed8] px-5 text-xs font-bold text-white shadow-lg shadow-[#2c68f5]/20 hover:opacity-95 transition">
            <i class="ti ti-plus text-base"></i> Tambah Agenda Baru
        </button>
    </div>

    @if(session('ok'))
    <div class="mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 text-sm text-emerald-800 font-medium flex items-center gap-2">
        <i class="ti ti-circle-check text-lg text-emerald-600"></i> {{ session('ok') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($schedules as $s)
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-school-line hover:shadow-md transition group">
            <div class="flex items-start justify-between mb-4">
                <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="ti ti-clock-bolt"></i>
                </div>
                <form action="{{ route('admin.schedules.destroy', $s) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?')">
                    @csrf @method('DELETE')
                    <button class="h-8 w-8 rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            </div>

            <h3 class="text-base font-black text-[#0f1e3d] mb-1">{{ $s->label }}</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ substr($s->start_time, 0, 5) }} — {{ substr($s->end_time, 0, 5) }}</p>

            <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                <span class="inline-flex px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $s->active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                    {{ $s->active ? 'Aktif' : 'Nonaktif' }}
                </span>
                <span class="text-[9px] font-bold text-slate-300">ID: #{{ $s->id }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
             <i class="ti ti-calendar-off text-5xl text-slate-300 mb-4"></i>
             <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Belum ada agenda jam</p>
        </div>
        @endforelse
    </div>

    {{-- Simple Add Modal --}}
    <dialog id="addScheduleModal" class="modal rounded-[32px] p-0 shadow-2xl border-none overflow-hidden max-w-md w-full backdrop:bg-black/50">
        <div class="bg-gradient-to-br from-[#0f1e3d] to-[#1a3a7a] p-8 text-white">
            <h3 class="text-lg font-black font-display uppercase tracking-wider">Agenda Sekolah Baru</h3>
        </div>
        <form method="POST" action="{{ route('admin.schedules.store') }}" class="p-8 space-y-5 bg-white">
            @csrf
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Nama / Keterangan Agenda</label>
                <input name="label" required class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none transition" placeholder="Misal: Masuk Pelajaran 1">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Jam Mulai</label>
                    <input name="start_time" type="time" required class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Jam Selesai</label>
                    <input name="end_time" type="time" required class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] outline-none">
                </div>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 h-12 bg-[#0f1e3d] text-white font-black text-xs uppercase tracking-widest rounded-2xl">Simpan Agenda</button>
                <button type="button" onclick="this.closest('dialog').close()" class="flex-1 h-12 bg-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest rounded-2xl">Batal</button>
            </div>
        </form>
    </dialog>
</div>

<style>
    .modal::backdrop { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
</style>
@endsection

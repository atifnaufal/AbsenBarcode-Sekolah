@extends('layouts.app')
@section('content')
<div class="max-w-[1440px] mx-auto px-4 py-8">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#2c68f5] text-[10px] font-black uppercase tracking-[0.15em] mb-3 border border-blue-100/50">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Manajemen Pengguna
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-[#0f1e3d] font-display capitalize tracking-tight">Data {{ $role === 'siswa' ? 'Seluruh Siswa' : 'Tenaga Pengajar' }}</h1>
            <p class="text-sm text-[#64748b] mt-2 font-medium">Kelola informasi identitas, grup kelas, dan hak akses anggota instansi.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create', $role) }}" class="h-12 inline-flex items-center gap-2 rounded-2xl bg-[#0f1e3d] px-6 text-xs font-black text-white shadow-xl shadow-[#0f1e3d]/20 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 uppercase tracking-widest">
                <i class="ti ti-plus text-base"></i>
                <span>Tambah {{ ucfirst($role) }}</span>
            </a>
        </div>
    </div>

    {{-- Controls Suite --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 mb-8">
        {{-- Search & Filter Bar --}}
        <div class="xl:col-span-3 bg-white rounded-[32px] border border-school-line p-5 shadow-sm">
            <form class="flex flex-col lg:flex-row gap-4">
                <div class="relative flex-1 group">
                    <i class="ti ti-search absolute left-4 top-1/2 -translate-y-1/2 text-lg text-slate-300 group-focus-within:text-[#2c68f5] transition-colors"></i>
                    <input name="q" value="{{ $q }}" placeholder="Cari nama, identitas, atau email..." class="w-full h-12 pl-12 pr-4 rounded-2xl border border-school-line bg-slate-50/50 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all placeholder:text-slate-400 placeholder:font-medium">
                </div>

                <div class="relative min-w-[220px]">
                    <select name="class_name" class="w-full h-12 pl-4 pr-10 rounded-2xl border border-school-line bg-slate-50/50 text-sm font-black text-[#0f1e3d] appearance-none focus:border-[#2c68f5] focus:bg-white outline-none transition-all cursor-pointer">
                        <option value="">Semua {{ $role === 'siswa' ? 'Kelas' : 'Unit' }}</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ $className === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    <i class="ti ti-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>

                <button class="h-12 px-8 rounded-2xl bg-[#2c68f5] text-xs font-black text-white hover:bg-[#1a56d6] hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-200 uppercase tracking-widest">
                    Terapkan
                </button>
            </form>
        </div>

        {{-- Registration Toggle Card --}}
        <div class="bg-white rounded-[32px] border border-school-line p-5 shadow-sm flex items-center justify-between group hover:border-[#2c68f5]/30 transition-all">
            <div class="flex items-center gap-4">
                <div class="h-11 w-11 rounded-2xl bg-blue-50 text-[#2c68f5] flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
                    <i class="ti ti-user-plus"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 leading-none">Pendaftaran</p>
                    <p class="text-xs font-black text-[#0f1e3d] mt-1.5 whitespace-nowrap">Daftar Mandiri</p>
                </div>
            </div>

            <form action="{{ route('admin.users.toggle-registration', $role) }}" method="POST" x-data x-ref="toggleForm" class="flex items-center">
                @csrf
                <label class="relative inline-flex items-center cursor-pointer scale-90">
                    @php
                        $isEnabled = $role === 'siswa' ? ($school->registration_enabled_students ?? false) : ($school->registration_enabled_teachers ?? false);
                    @endphp
                    <input type="checkbox" name="enabled" value="1" {{ $isEnabled ? 'checked' : '' }} class="sr-only peer" @change="$refs.toggleForm.submit()">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2c68f5]"></div>
                </label>
            </form>
        </div>
    </div>

    {{-- Main Content Table --}}
    <div class="bg-white rounded-[40px] border border-school-line shadow-xl shadow-slate-200/50 overflow-hidden">
        @if($users->isEmpty())
            {{-- Professional Empty State --}}
            <div class="py-24 text-center max-w-sm mx-auto flex flex-col items-center">
                <div class="h-24 w-24 rounded-[32px] bg-slate-50 flex items-center justify-center mb-6 border border-slate-100 shadow-inner">
                    <i class="ti ti-users-group text-5xl text-slate-200"></i>
                </div>
                <h3 class="text-xl font-black text-[#0f1e3d] mb-2 tracking-tight">Database Kosong</h3>
                <p class="text-xs text-slate-400 font-bold leading-relaxed uppercase tracking-widest">
                    Belum ada data anggota untuk kriteria ini.
                </p>
                <a href="{{ route('admin.users.create', $role) }}" class="mt-8 h-11 inline-flex items-center px-6 bg-[#f8fafc] border border-slate-200 rounded-xl text-[10px] font-black text-[#0f1e3d] uppercase tracking-widest hover:bg-slate-100 transition-all">
                    Input Data Baru
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#f8f9fc] border-b border-school-line">
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Identitas Anggota</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Kontak Email</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Grup / Kelas</th>
                            <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Status</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Aksi Operasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-school-line">
                        @foreach($users as $u)
                        <tr class="group hover:bg-slate-50/50 transition-all duration-150">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl bg-white border border-school-line shadow-sm overflow-hidden flex items-center justify-center text-sm font-black text-[#2c68f5] flex-shrink-0 group-hover:scale-105 transition-transform">
                                        @if($u->avatar_url)
                                            <img src="{{ $u->avatar_url }}" class="h-full w-full object-cover">
                                        @else
                                            {{ str($u->name)->substr(0,1)->upper() }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-black text-[#0f1e3d] text-sm group-hover:text-[#2c68f5] transition-colors">{{ $u->name }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] text-slate-400 font-bold font-mono tracking-tighter uppercase">{{ $role === 'siswa' ? 'NISN' : 'NIP' }}: {{ $u->identifier }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-[#0f1e3d]">{{ $u->email }}</span>
                                    <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest mt-0.5">Akun Terverifikasi</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="inline-flex px-3 py-1 rounded-xl text-[10px] font-black bg-white text-[#2c68f5] border border-blue-100 shadow-sm uppercase tracking-wider">
                                    {{ $u->class_name ?: 'NON-GRUP' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="inline-flex h-2 w-2 rounded-full {{ $u->active ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300' }} mr-2"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest {{ $u->active ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $u->active ? 'Aktif' : 'Off' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', [$role, $u]) }}" class="h-9 w-9 rounded-xl bg-blue-50 text-[#2c68f5] flex items-center justify-center hover:bg-[#2c68f5] hover:text-white transition-all shadow-sm active:scale-95">
                                        <i class="ti ti-edit text-lg"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', [$role, $u]) }}" onsubmit="return confirm('Hapus pengguna ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="h-9 w-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm active:scale-95">
                                            <i class="ti ti-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Responsive Footer --}}
            <div class="px-8 py-6 border-t border-school-line bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} Anggota</p>
                <div class="pagination-custom">
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Fix overlap on smaller desktop screens */
    @media (min-width: 1024px) and (max-width: 1280px) {
        .xl\:grid-cols-4 {
            grid-template-cols: repeat(1, minmax(0, 1fr));
        }
    }
</style>
@endsection

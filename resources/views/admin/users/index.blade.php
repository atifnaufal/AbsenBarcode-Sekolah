@extends('layouts.app')
@section('content')
<div class="max-w-[1280px] mx-auto px-2 sm:px-4">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="text-[10px] font-bold uppercase tracking-wider text-[#623ed8] mb-1">Manajemen Pengguna Instansi</div>
            <h1 class="text-2xl font-black text-[#0f1e3d] font-display capitalize">Data Anggota {{ $role }}</h1>
        </div>
        <a href="{{ route('admin.users.create', $role) }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#2c68f5] to-[#623ed8] px-5 text-xs font-bold text-white shadow-lg shadow-[#2c68f5]/20 hover:opacity-95 transition">
            <i class="ti ti-plus text-base"></i>
            Tambah {{ ucfirst($role) }} Baru
        </a>
    </div>

    {{-- Filter Search Box --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-school-line p-4 shadow-sm">
            <form class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="ti ti-search absolute left-4 top-1/2 -translate-y-1/2 text-lg text-[#8a95a8]"></i>
                    <input name="q" value="{{ $q }}" placeholder="Cari nama, identitas, atau email..." class="w-full h-11 pl-11 pr-4 rounded-xl border border-school-line text-sm text-[#172033] focus:border-[#2c68f5] focus:outline-none transition">
                </div>

                {{-- NEW: Class Filter --}}
                <div class="relative min-w-[180px]">
                    <select name="class_name" class="w-full h-11 pl-4 pr-10 rounded-xl border border-school-line text-sm font-bold text-[#0f1e3d] appearance-none focus:border-[#2c68f5] focus:outline-none transition bg-white">
                        <option value="">Semua {{ $role === 'siswa' ? 'Kelas' : 'Unit' }}</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ $className === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    <i class="ti ti-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[#8a95a8] pointer-events-none"></i>
                </div>

                <button class="h-11 px-6 rounded-xl bg-[#0f1e3d] text-xs font-bold text-white hover:bg-[#1a2d52] transition">
                    Terapkan Filter
                </button>
            </form>
        </div>

        {{-- NEW: Registration Toggle Card --}}
        <div class="bg-white rounded-2xl border border-school-line p-4 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-blue-50 text-[#2c68f5] flex items-center justify-center text-xl">
                    <i class="ti ti-user-plus"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-[#64748b] uppercase tracking-widest leading-none">Pendaftaran</p>
                    <p class="text-xs font-bold text-[#0f1e3d] mt-1">Status Pendaftaran {{ ucfirst($role) }}</p>
                </div>
            </div>

            <form action="{{ route('admin.users.toggle-registration', $role) }}" method="POST" x-data x-ref="toggleForm">
                @csrf
                <label class="relative inline-flex items-center cursor-pointer">
                    @php
                        $isEnabled = $role === 'siswa' ? ($school->registration_enabled_students ?? false) : ($school->registration_enabled_teachers ?? false);
                    @endphp
                    <input type="checkbox" name="enabled" value="1" {{ $isEnabled ? 'checked' : '' }} class="sr-only peer" @change="$refs.toggleForm.submit()">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2c68f5]"></div>
                </label>
            </form>
        </div>
    </div>

    {{-- Feedback Alert --}}
    @if(session('ok'))
    <div class="mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 text-sm text-emerald-800 font-medium flex items-center gap-2">
        <i class="ti ti-circle-check text-lg text-emerald-600"></i>
        {{ session('ok') }}
    </div>
    @endif

    {{-- Main Panel Grid with Empty States handling --}}
    <div class="bg-white rounded-3xl border border-school-line shadow-sm overflow-hidden">
        @if($users->isEmpty())
            {{-- Elite Empty State Render --}}
            <div class="p-12 text-center max-w-sm mx-auto flex flex-col items-center justify-center">
                <div class="relative w-24 h-24 mb-4 flex items-center justify-center rounded-3xl bg-gradient-to-tr from-[#2c68f5]/10 to-[#623ed8]/10 text-4xl text-[#623ed8]">
                    <i class="ti ti-users-group"></i>
                    <div class="absolute -bottom-1 -right-1 h-6 w-6 rounded-lg bg-amber-400 text-white text-xs flex items-center justify-center font-bold shadow-md">!</div>
                </div>
                <h3 class="text-base font-bold text-[#0f1e3d]">Data Kosong / Tidak Ditemukan</h3>
                <p class="text-xs text-[#8a95a8] mt-1 leading-relaxed">
                    Sistem belum mendeteksi entri pengguna dengan kriteria pencarian tersebut atau tabel basis data masih kosong.
                </p>
                <a href="{{ route('admin.users.create', $role) }}" class="mt-4 inline-flex h-9 items-center justify-center px-4 bg-[#f2f5fa] border border-school-line rounded-lg text-xs font-bold text-[#623ed8] hover:bg-[#623ed8]/5 transition">
                    Masukkan Data Pertama
                </a>
            </div>
        @else
            {{-- Big Screen Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#f8f9fc] text-xs font-bold uppercase tracking-wider text-[#68748b] border-b border-school-line">
                        <tr>
                            <th class="px-6 py-4 text-left">Nama Lengkap</th>
                            <th class="px-6 py-4 text-left">NISN / Nomor Induk</th>
                            <th class="px-6 py-4 text-left">Alamat Email</th>
                            <th class="px-6 py-4 text-left">Grup / Kelas</th>
                            <th class="px-6 py-4 text-right">Aksi Operasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-school-line text-[#172033]">
                        @foreach($users as $u)
                        <tr class="hover:bg-[#f8f9fc]/50 transition duration-150">
                            <td class="px-6 py-4 font-semibold text-[#0f1e3d] flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-[#2c68f5]/10 flex items-center justify-center text-xs font-bold text-[#2c68f5] overflow-hidden border border-school-line shadow-sm">
                                    @if($u->avatar_url)
                                        <img src="{{ $u->avatar_url }}" class="h-full w-full object-cover">
                                    @else
                                        {{ str($u->name)->substr(0,1)->upper() }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-[#0f1e3d]">{{ $u->name }}</p>
                                    <p class="text-[10px] text-[#8a95a8] font-mono mt-0.5">{{ $u->identifier }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-[#68748b]">{{ $u->identifier }}</td>
                            <td class="px-6 py-4 text-[#68748b]">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-[#f2f5fa] text-[#623ed8]">
                                    {{ $u->class_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3 justify-end">
                                    <a href="{{ route('admin.users.edit', [$role, $u]) }}" class="text-xs font-bold text-[#2c68f5] hover:underline">
                                        Edit
                                    </a>
                                    <span class="h-3 w-px bg-school-line"></span>
                                    <form method="POST" action="{{ route('admin.users.destroy', [$role, $u]) }}" onsubmit="return confirm('Apakah Anda sepenuhnya yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-bold text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Screen List --}}
            <div class="sm:hidden divide-y divide-school-line">
                @foreach($users as $u)
                <div class="p-4 space-y-2 hover:bg-[#f8f9fc]/40 transition">
                    <div class="flex items-center justify-between gap-2">
                        <div class="font-bold text-[#0f1e3d] text-sm">{{ $u->name }}</div>
                        <span class="text-[10px] font-mono font-bold bg-[#f2f5fa] text-[#623ed8] px-2 py-0.5 rounded">
                            {{ $u->class_name ?? '-' }}
                        </span>
                    </div>
                    <div class="text-xs text-[#68748b] font-mono">{{ $u->identifier }} • {{ $u->email }}</div>
                    <div class="flex gap-4 pt-1 items-center">
                        <a href="{{ route('admin.users.edit', [$role, $u]) }}" class="text-xs font-bold text-[#2c68f5]">
                            <i class="ti ti-edit mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('admin.users.destroy', [$role, $u]) }}" onsubmit="return confirm('Hapus?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-bold text-red-600">
                                <i class="ti ti-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination Footer --}}
            <div class="p-4 border-t border-school-line bg-[#f8f9fc]">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

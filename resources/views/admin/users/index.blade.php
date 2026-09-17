@extends('layouts.app')
@section('content')
<div class="max-w-[1440px] mx-auto px-4 py-8">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-6 mb-8">
        <div>
            <div class="text-xs font-bold uppercase tracking-wider text-[#623ed8] mb-1.5">Manajemen Pengguna Instansi</h1>
            <h1 class="text-3xl font-extrabold text-[#0f1e3d] font-display sm:text-4xl capitalize">Data Anggota {{ $role }}</h1>
        </div>
        <a href="{{ route('admin.users.create', $role) }}" class="relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#2c68f5] to-[#623ed8] px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-[#2c68f5]/20 hover:opacity-95 hover:shadow-xl transition-all duration-200">
            <i class="ti ti-plus text-base"></i>
            <span>Tambah {{ ucfirst($role) }} Baru</span>
        </a>
    </div>

    {{-- Filter & Actions Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-school-line p-6 shadow-sm">
            <form class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <i class="ti ti-search absolute left-4 top-1/2 -translate-y-1/2 text-lg text-[#8a95a8]"></i>
                    <input name="q" value="{{ $q }}" placeholder="Cari nama, identitas, atau email..." class="w-full h-12 pl-12 pr-4 rounded-xl border border-school-line text-sm text-[#172033] focus:border-[#2c68f5] focus:outline-none transition placeholder-gray-400">
                </div>

                <div class="relative min-w-[200px]">
                    <select name="class_name" class="w-full h-12 pl-4 pr-10 rounded-xl border border-school-line text-sm font-bold text-[#0f1e3d] appearance-none focus:border-[#2c68f5] focus:outline-none transition bg-white">
                        <option value="">Semua {{ $role === 'siswa' ? 'Kelas' : 'Unit' }}</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ $className === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    <i class="ti ti-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[#8a95a8] pointer-events-none"></i>
                </div>

                <button class="h-12 flex-1 rounded-xl bg-[#0f1e3d] text-sm font-bold text-white hover:bg-[#1a2d52] transition-colors duration-200">
                    Terapkan Filter
                </button>
            </form>
        </div>

        <div class="bg-white rounded-3xl border border-school-line p-6 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-blue-50 text-[#2c68f5] flex items-center justify-center text-xl">
                    <i class="ti ti-user-plus"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-widest text-[#64748b]">Pendaftaran</p>
                    <p class="text-base font-bold text-[#0f1e3d]">Status Pendaftaran {{ ucfirst($role) }}</p>
                </div>
            </div>

            <form action="{{ route('admin.users.toggle-registration', $role) }}" method="POST" x-data x-ref="toggleForm" class="inline">
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
    <div class="mb-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 px-5 py-3 flex items-center gap-3 text-sm font-medium text-emerald-800">
        <i class="ti ti-circle-check text-lg text-emerald-600"></i>
        {{ session('ok') }}
    </div>
    @endif

    {{-- Main Content --}}
    <div class="bg-white rounded-3xl border border-school-line shadow-sm overflow-hidden">
        @if($users->isEmpty())
            {{-- Elegant Empty State --}}
            <div class="p-10 text-center max-w-md mx-auto">
                <div class="relative w-28 h-28 mb-6 flex items-center justify-center rounded-3xl bg-gradient-to-tr from-[#2c68f5]/15 to-[#623ed8]/10 mx-auto">
                    <i class="ti ti-users-group text-6xl text-[#623ed8]"></i>
                </div>
                <h2 class="text-xl font-bold text-[#0f1e3d] mb-2">Data Kosong / Tidak Ditemukan</h2>
                <p class="text-sm text-[#8a95a8] mb-6 leading-relaxed">
                    Sistem belum mendeteksi entri pengguna dengan kriteria pencarian tersebut atau tabel basis data masih kosong.
                </p>
                <a href="{{ route('admin.users.create', $role) }}" class="inline-flex items-center gap-2 rounded-lg bg-[#f2f5fa] border border-[#623ed8]/50 px-5 py-2.5 text-xs font-bold text-[#623ed8] hover:bg-[#623ed8]/10 transition-colors">
                    <i class="ti ti-user-plus"></i>
                    Masukkan Data Pertama
                </a>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#f8f9fc] border-b border-school-line">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-[#68748b]">Nama Lengkap</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-[#68748b]">NISN / Nomor Induk</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-[#68748b]">Alamat Email</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-[#68748b]">Grup / Kelas</th>
                            <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider text-[#68748b]">Aksi Operasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-school-line">
                        @foreach($users as $u)
                        <tr class="group hover:bg-[#f8f9fc]/30 transition-colors duration-150">
                            <td class="px-6 py-4 font-medium text-[#0f1e3d]">
                                <div class="h-10 w-10 rounded-xl bg-[#2c68f5]/10 flex items-center justify-center text-xs font-bold text-[#2c68f5] overflow-hidden border border-school-line shadow-sm flex-shrink-0">
                                    @if($u->avatar_url)
                                        <img src="{{ $u->avatar_url }}" class="h-full w-full object-cover">
                                    @else
                                        {{ str($u->name)->substr(0,1)->upper() }}
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="font-bold text-[#0f1e3d]">{{ $u->name }}</p>
                                    <p class="text-xs text-[#8a95a8] font-mono mt-0.5">{{ $u->identifier }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-[#68748b]">{{ $u->identifier }}</td>
                            <td class="px-6 py-4 text-[#68748b]">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-[#f2f5fa] text-[#623ed8] border border-[#623ed8]/10 shadow-sm">
                                    {{ $u->class_name ?: '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', [$role, $u]) }}" class="text-xs font-bold text-[#2c68f5] hover:underline transition-colors">
                                        Edit
                                    </a>
                                    <span class="h-2 w-px bg-school-line"></span>
                                    <form method="POST" action="{{ route('admin.users.destroy', [$role, $u]) }}" onsubmit="return confirm('Apakah Anda sepenuhnya yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-bold text-red-600 hover:underline transition-colors">
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
                <article class="p-5 border-b border-school-line last:border-b-0 hover:bg-[#f8f9fc]/30 transition-colors duration-150">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-medium text-[#0f1e3d] text-sm">{{ $u->name }}</div>
                        <span class="text-xs font-bold bg-[#f2f5fa] text-[#623ed8] px-2 py-1 rounded">{{ $u->class_name ?? '-' }}</span>
                    </div>
                    <div class="text-xs text-[#68748b] font-mono mt-1">{{ $u->identifier }} • {{ $u->email }}</div>
                    <div class="flex gap-3 pt-2 items-center">
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
                </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="p-6 border-t border-school-line bg-[#f8f9fc]">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

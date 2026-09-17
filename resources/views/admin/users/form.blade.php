@extends('layouts.app')
@section('content')
<div class="max-w-[720px] mx-auto py-12 px-4">
    <div class="mb-10 text-center animate-[fadeIn_.6s_ease]">
        <div class="inline-flex h-16 w-16 rounded-3xl bg-blue-50 text-[#2c68f5] items-center justify-center text-3xl shadow-inner mb-4">
            <i class="ti {{ $user->exists ? 'ti-user-edit' : 'ti-user-plus' }}"></i>
        </div>
        <h1 class="text-3xl font-black text-[#0f1e3d] font-display tracking-tight">{{ $user->exists ? 'Perbarui Data' : 'Tambah Anggota' }} {{ ucfirst($role) }}</h1>
        <p class="text-sm text-[#64748b] mt-2 font-medium">Lengkapi formulir di bawah untuk memproses data ke database sistem.</p>
    </div>

    {{-- Admin Pro-Tip --}}
    <div class="mb-8 bg-amber-50 border border-amber-100 rounded-3xl p-5 flex items-start gap-4 animate-[slideIn_.5s_ease-out]">
        <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0"><i class="ti ti-alert-triangle text-xl"></i></div>
        <div>
            <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-1">Peringatan Input Data</p>
            <p class="text-xs text-amber-800/80 font-medium leading-relaxed">
                Pastikan <b>{{ $role === 'siswa' ? 'NISN' : 'NIP' }}</b> unik dan tidak duplikat. Sistem akan menolak jika email atau identitas sudah terdaftar.
                @if($role === 'guru') Nama kelas akan otomatis diawali kata <b>'Wali Kelas'</b> jika belum ada. @endif
            </p>
        </div>
    </div>

    <div class="bg-white rounded-[40px] border border-school-line shadow-2xl shadow-slate-200/50 p-8 sm:p-12 relative overflow-hidden group animate-[fadeIn_.8s_ease]">
        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:scale-110 transition-transform duration-700">
            <i class="ti ti-shield-check text-9xl"></i>
        </div>

        <form method="POST" action="{{ $user->exists ? route('admin.users.update',[$role,$user]) : route('admin.users.store',$role) }}" class="space-y-8 relative z-10">
            @csrf
            @if($user->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-8">
                {{-- Full Name --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Nama Lengkap Sesuai Identitas</label>
                    <div class="relative">
                        <i class="ti ti-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input name="name" value="{{ old('name',$user->name) }}" required class="w-full h-14 pl-12 pr-4 rounded-2xl border border-school-line bg-slate-50/50 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all" placeholder="Masukkan nama lengkap">
                    </div>
                    @error('name')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Alamat Email Aktif</label>
                    <div class="relative">
                        <i class="ti ti-mail absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input name="email" type="email" value="{{ old('email',$user->email) }}" required class="w-full h-14 pl-12 pr-4 rounded-2xl border border-school-line bg-slate-50/50 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all" placeholder="email@instansi.sch.id">
                    </div>
                    @error('email')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Identifier --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">{{ $role === 'siswa' ? 'Nomor Induk (NISN)' : 'Nomor Pegawai (NIP)' }}</label>
                        <div class="relative">
                            <i class="ti ti-id-badge absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            <input name="identifier" value="{{ old('identifier',$user->identifier) }}" required class="w-full h-14 pl-12 pr-4 rounded-2xl border border-school-line bg-slate-50/50 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition-all" placeholder="Digit angka unik">
                        </div>
                        @error('identifier')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Class/Group --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">{{ $role === 'siswa' ? 'Penempatan Kelas' : 'Wali Kelas Dari' }}</label>
                        <div class="relative">
                            <i class="ti ti-school absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            <input name="class_name" value="{{ old('class_name',$user->class_name) }}" required class="w-full h-14 pl-12 pr-4 rounded-2xl border border-school-line bg-slate-50/50 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition-all" placeholder="{{ $role === 'siswa' ? 'Contoh: XI RPL 1' : 'Contoh: XI RPL (Otomatis Wali)' }}">
                        </div>
                        @error('class_name')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Kata Sandi Akses {{ $user->exists ? '(Kosongkan jika tidak diubah)' : '' }}</label>
                    <div class="relative">
                        <i class="ti ti-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input name="password" type="password" {{ $user->exists ? '' : 'required' }} class="w-full h-14 pl-12 pr-4 rounded-2xl border border-school-line bg-slate-50/50 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white outline-none transition-all" placeholder="Min. 6 karakter">
                    </div>
                    @error('password')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                {{-- Status --}}
                @if($user->exists)
                <div class="p-5 rounded-[24px] bg-slate-50 border border-school-line flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-white flex items-center justify-center text-[#2c68f5] shadow-sm">
                            <i class="ti ti-shield-check text-xl"></i>
                        </div>
                        <span class="text-xs font-black text-[#0f1e3d] uppercase tracking-widest">Status Akun Aktif</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" value="1" @checked($user->active) class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>
                @endif
            </div>

            <div class="pt-6 flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 h-14 bg-[#0f1e3d] text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-[#0f1e3d]/20 hover:bg-black hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    {{ $user->exists ? 'Simpan Perubahan' : 'Daftarkan Anggota' }}
                </button>
                <a href="{{ route('admin.users.index',$role) }}" class="flex-1 h-14 bg-slate-100 text-slate-500 font-black text-xs uppercase tracking-[0.2em] rounded-2xl flex items-center justify-center hover:bg-slate-200 transition-all duration-200">
                    Batalkan & Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

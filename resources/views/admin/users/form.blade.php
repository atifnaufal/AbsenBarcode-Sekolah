@extends('layouts.app')
@section('content')
<div class="max-w-[600px] mx-auto school-panel p-6 sm:p-8">
<h1 class="school-display text-xl font-bold mb-6">{{ $user->exists ? 'Edit' : 'Tambah' }} {{ ucfirst($role) }}</h1>
<form method="POST" action="{{ $user->exists ? route('admin.users.update',[$role,$user]) : route('admin.users.store',$role) }}" class="space-y-4">
@csrf @if($user->exists) @method('PUT') @endif
<div><label class="text-sm font-semibold">Nama</label><input name="name" value="{{ old('name',$user->name) }}" required class="mt-1 w-full rounded-xl border border-school-line px-3 py-2.5 text-sm"></div>
<div><label class="text-sm font-semibold">Email</label><input name="email" type="email" value="{{ old('email',$user->email) }}" required class="mt-1 w-full rounded-xl border border-school-line px-3 py-2.5 text-sm"></div>
<div><label class="text-sm font-semibold">NISN / NIP</label><input name="identifier" value="{{ old('identifier',$user->identifier) }}" required class="mt-1 w-full rounded-xl border border-school-line px-3 py-2.5 text-sm"></div>
<div><label class="text-sm font-semibold">{{ $role === 'siswa' ? 'Kelas' : 'Wali Kelas' }}</label><input name="class_name" value="{{ old('class_name',$user->class_name) }}" required class="mt-1 w-full rounded-xl border border-school-line px-3 py-2.5 text-sm" placeholder="{{ $role === 'siswa' ? 'Contoh: XI RPL' : 'Contoh: XI RPL (Otomatis Wali Kelas)' }}"></div>
<div><label class="text-sm font-semibold">Password {{ $user->exists ? '(kosongkan jika tidak ganti)' : '' }}</label><input name="password" type="password" class="mt-1 w-full rounded-xl border border-school-line px-3 py-2.5 text-sm"></div>
@if($user->exists)<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="active" value="1" @checked($user->active) class="rounded"> Aktif</label>@endif
<div class="flex gap-3"><button class="school-button school-button-primary flex-1">Simpan</button><a href="{{ route('admin.users.index',$role) }}" class="school-button school-button-secondary flex-1 text-center">Batal</a></div>
</form>
</div>
@endsection

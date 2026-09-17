@php
    $isAdmin = auth()->user()->role === \App\Enums\UserRole::ADMIN_SEKOLAH;
    $navItems = $isAdmin ? [
        ['route' => 'dashboard', 'label' => 'Absensi hari ini', 'icon' => 'ti-layout-dashboard'],
        ['route' => 'monitor', 'label' => 'Layar QR', 'icon' => 'ti-qrcode'],
    ] : [
        ['route' => 'student.dashboard', 'label' => 'Absensi', 'icon' => 'ti-calendar-event'],
        ['route' => 'attendance.scan', 'label' => 'Layar Absen', 'icon' => 'ti-qrcode'],
        ['route' => 'student.profile', 'label' => 'Profil & Pengaturan', 'icon' => 'ti-settings'],
    ];
@endphp

<aside class="school-sidebar hidden w-[232px] shrink-0 flex-col px-5 py-5 xl:flex xl:min-h-screen" aria-label="Navigasi administrasi">
    <div class="mb-8 flex items-center gap-3">
        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK Bina Utama Kendal" class="h-12 w-12 xl:h-14 xl:w-14 shrink-0 rounded-2xl bg-white p-1 shadow-lg flex items-center justify-center animate-[tilt3d_5s_ease-in-out_infinite]" style="transform: perspective(600px) rotateY(-8deg) rotateX(6deg);">
        <div>
            <div class="school-display text-xs font-semibold leading-4 text-white">SMK BINA</div>
            <div class="text-xs leading-4 text-white/55">UTAMA KENDAL</div>
        </div>
    </div>

    <div class="mb-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/50">{{ $isAdmin ? 'Administrasi' : 'Menu Utama' }}</div>
    <nav class="space-y-1">
        @foreach ($navItems as $item)
            @php $isActive = $active === $item['route']; @endphp
            <a
                href="{{ route($item['route']) }}"
                @if ($isActive) aria-current="page" @endif
                class="flex h-[42px] w-full items-center gap-3 rounded-school-control px-3 text-left text-[13px] font-semibold transition-colors {{ $isActive ? 'bg-gradient-to-r from-school-purple to-school-blue text-white' : 'text-white/65 hover:bg-white/10 hover:text-white' }}"
            >
                <i class="ti {{ $item['icon'] }} text-[18px]" aria-hidden="true"></i>
                {{ $item['label'] }}
            </a>
        @endforeach

        @if($isAdmin)
        @php $crudNav=[
            ['route'=>'admin.users.index','param'=>['role'=>'guru'],'label'=>'Data guru','icon'=>'ti-users'],
            ['route'=>'admin.users.index','param'=>['role'=>'siswa'],'label'=>'Data siswa','icon'=>'ti-school'],
            ['route'=>'admin.schedules.index','param'=>[],'label'=>'Agenda Jadwal','icon'=>'ti-calendar-time'],
            ['route'=>'admin.reports.index','param'=>[],'label'=>'Laporan','icon'=>'ti-file-spreadsheet'],
            ['route'=>'admin.location.edit','param'=>[],'label'=>'Aktivasi Pengaturan','icon'=>'ti-settings-automation']
        ]; @endphp
        @foreach ($crudNav as $item)
            <a href="{{ route($item['route'],$item['param']) }}" class="flex h-[42px] w-full items-center gap-3 rounded-school-control px-3 text-left text-[13px] font-semibold {{ request()->routeIs($item['route'].'*') ? 'text-white bg-white/10' : 'text-white/65 hover:bg-white/10 hover:text-white' }}">
                <i class="ti {{ $item['icon'] }} text-[18px]"></i>{{ $item['label'] }}
            </a>
        @endforeach
        @endif
    </nav>

    <div class="mt-auto border-t border-white/10 pt-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-3 text-[13px] text-white/55 transition-colors hover:text-white">
                <i class="ti ti-logout text-[17px]" aria-hidden="true"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>

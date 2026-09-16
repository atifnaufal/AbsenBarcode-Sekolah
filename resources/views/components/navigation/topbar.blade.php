<header class="border-b border-school-line bg-white px-4 py-3 sm:px-6 xl:px-7">
    <div class="flex min-h-10 items-center justify-between gap-4">
        <div class="flex min-w-0 items-center gap-3">
            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-school-control bg-school-navy text-[11px] font-bold text-white xl:hidden">BU</div>
            <div class="min-w-0 text-[13px] text-school-muted">
                <span class="hidden sm:inline">Administrasi <span class="mx-2 text-[#ccd4e0]">/</span></span>
                <span class="font-semibold text-school-ink">{{ $active === 'monitor' ? 'Layar QR' : 'Absensi hari ini' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden items-center gap-2 text-xs text-school-muted sm:flex">
                <span class="h-2 w-2 rounded-full bg-school-success" aria-hidden="true"></span>
                Server tersambung
            </div>
            <div class="grid h-8 w-8 place-items-center rounded-full bg-[#e9e4ff] text-xs font-bold text-school-purple overflow-hidden border border-school-line">
                @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" class="h-full w-full object-cover">
                @else
                    {{ str($activeUser->name ?? auth()->user()?->name ?? 'AS')->substr(0, 2)->upper() }}
                @endif
            </div>
            <details class="relative xl:hidden">
                <summary class="grid h-9 w-9 cursor-pointer list-none place-items-center rounded-school-control border border-school-line text-school-muted hover:bg-school-canvas" aria-label="Buka menu navigasi">
                    <i class="ti ti-menu-2 text-lg" aria-hidden="true"></i>
                </summary>
                <nav class="absolute right-0 top-11 z-20 w-52 rounded-school-card border border-school-line bg-white p-2 shadow-school-section">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-school-control px-3 py-2.5 text-sm font-semibold text-school-ink hover:bg-school-canvas"><i class="ti ti-layout-dashboard" aria-hidden="true"></i>Absensi hari ini</a>
                    <a href="{{ route('monitor') }}" class="flex items-center gap-3 rounded-school-control px-3 py-2.5 text-sm font-semibold text-school-ink hover:bg-school-canvas"><i class="ti ti-qrcode" aria-hidden="true"></i>Layar QR</a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-1 border-t border-school-line pt-1">@csrf<button type="submit" class="flex w-full items-center gap-3 rounded-school-control px-3 py-2.5 text-left text-sm font-semibold text-school-muted hover:bg-school-canvas"><i class="ti ti-logout" aria-hidden="true"></i>Keluar</button></form>
                </nav>
            </details>
        </div>
    </div>
</header>

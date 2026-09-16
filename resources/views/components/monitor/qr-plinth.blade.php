@props(['qr'])

<div class="qr-plinth mx-auto w-full max-w-[320px]" x-data="{ loaded: false }">
    <div class="relative rounded-[24px] bg-white p-4 shadow-xl border border-school-line overflow-hidden">
        {{-- Skeleton Loader / Spinner --}}
        <div x-show="!loaded" class="absolute inset-0 z-20 bg-white flex flex-col items-center justify-center">
            <div class="h-12 w-12 border-4 border-slate-100 border-t-[#2c68f5] rounded-full animate-spin"></div>
            <p class="mt-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest animate-pulse">Generating QR...</p>
        </div>

        <div class="flex aspect-square items-center justify-center rounded-[16px] bg-white p-1">
            <img
                :src="qr.qr_url"
                src="{{ $qr['qr_url'] }}"
                @load="loaded = true"
                x-init="$watch('qr.qr_url', () => loaded = false)"
                alt="QR absensi dinamis SMK BINA UTAMA KENDAL"
                width="520"
                height="520"
                class="aspect-square w-full object-contain transition-opacity duration-300"
                :class="loaded ? 'opacity-100' : 'opacity-0'"
            >
        </div>
        <div class="mt-4 flex items-center justify-between gap-3 border-t border-school-line pt-3 text-[11px] text-school-muted">
            <span class="flex items-center gap-1.5"><i class="ti ti-refresh" aria-hidden="true"></i>Token berganti otomatis</span>
            <span class="font-semibold tabular-nums text-school-ink" x-text="qr.id ? `#${String(qr.id).padStart(4, '0')}` : '#—'">#{{ str_pad((string) ($qr['id'] ?? 0), 4, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>
</div>

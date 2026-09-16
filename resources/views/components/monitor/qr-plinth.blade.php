@props(['qr'])

<div class="qr-plinth mx-auto w-full max-w-[320px]">
    <div class="relative rounded-[24px] bg-white p-4 shadow-xl border border-school-line">
        <div class="flex aspect-square items-center justify-center rounded-[16px] bg-white p-1">
            <img
                :src="qr.qr_url"
                src="{{ $qr['qr_url'] }}"
                alt="QR absensi dinamis SMK BINA UTAMA KENDAL"
                width="520"
                height="520"
                decoding="async"
                loading="lazy"
                class="aspect-square w-full object-contain"
            >
        </div>
        <div class="mt-4 flex items-center justify-between gap-3 border-t border-school-line pt-3 text-[11px] text-school-muted">
            <span class="flex items-center gap-1.5"><i class="ti ti-refresh" aria-hidden="true"></i>Token berganti otomatis</span>
            <span class="font-semibold tabular-nums text-school-ink" x-text="qr.id ? `#${String(qr.id).padStart(4, '0')}` : '#—'">#{{ str_pad((string) ($qr['id'] ?? 0), 4, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>
</div>

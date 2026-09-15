@extends('layouts.app')

@section('content')
    <div
        class="mx-auto max-w-[1280px]"
        x-data="monitorCountdown({
            initial: {{ Illuminate\Support\Js::from($activeQr) }},
            refreshUrl: '{{ route('monitor.refresh') }}',
            isClosed: {{ $isClosed ? 'true' : 'false' }},
            label: '{{ $schedule['label'] }}'
        })"
        x-init="init(); startPoll('{{ route('monitor.recent') }}')"
        @monitor-error.window="window.alert($event.detail.message)"
    >
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="mb-1 text-[10px] font-semibold uppercase tracking-[0.17em] text-school-purple">Mode monitor</div>
                <h1 class="school-display text-2xl font-semibold">Layar QR {{ $schedule['label'] }}</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="school-button school-button-secondary"><i class="ti ti-arrow-left" aria-hidden="true"></i>Kembali ke dashboard</a>
        </div>

        <section class="school-panel overflow-hidden" aria-labelledby="monitor-title">
            <x-monitor.monitor-header />

            {{-- Template if Closed --}}
            <template x-if="isClosed">
                <div class="bg-school-canvas p-10 sm:p-20 text-center flex flex-col items-center justify-center">
                    <div class="h-24 w-24 rounded-3xl bg-white shadow-xl flex items-center justify-center text-5xl text-school-muted mb-6">
                        <i class="ti ti-clock-off"></i>
                    </div>
                    <h2 class="school-display text-3xl font-bold text-school-navy">Sesi Absensi Ditutup</h2>
                    <p class="mt-4 text-school-muted max-w-md mx-auto">
                        Maaf, sesi <strong>{{ $schedule['label'] }}</strong> belum dimulai atau sudah berakhir.<br>
                        Jadwal: <span class="font-bold text-school-navy">{{ substr($schedule['start'], 0, 5) }} - {{ substr($schedule['end'], 0, 5) }}</span>
                    </p>
                    <div class="mt-8 px-6 py-3 rounded-full bg-white border border-school-line text-xs font-bold text-school-soft uppercase tracking-widest">
                        Menunggu jadwal operasional berikutnya
                    </div>
                </div>
            </template>

            {{-- Original Content if Open --}}
            <template x-if="!isClosed">
                <div class="grid lg:grid-cols-[58%_42%]">
                    <div class="bg-school-canvas px-5 py-8 sm:px-10 sm:py-12">
                        <div class="mx-auto flex max-w-[520px] items-end justify-between gap-4">
                            <div>
                                <h2 class="school-display text-xl font-semibold sm:text-2xl">QR absensi aktif</h2>
                                <p class="mt-1 text-sm text-school-muted">Token berganti otomatis setiap 15 detik</p>
                            </div>
                            <span class="flex shrink-0 items-center gap-2 text-xs font-semibold text-school-success"><span class="h-2 w-2 rounded-full bg-school-success"></span>Siap dipindai</span>
                        </div>
                        <div class="mt-8">@if($activeQr)<x-monitor.qr-plinth :qr="$activeQr" />@else<div class="text-center py-10 text-school-muted">Menunggu jadwal QR...</div>@endif</div>
                        <div class="mx-auto mt-6 flex max-w-[430px] items-center justify-between rounded-school-control border border-school-line bg-white px-4 py-3 text-xs text-school-muted">
                            <span class="flex items-center gap-2"><i class="ti ti-refresh text-school-purple" aria-hidden="true"></i>Terakhir diperbarui <strong class="tabular-nums text-school-ink" x-text="qr.issued_at ? new Date(qr.issued_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '—'">{{ now(config('attendance.timezone'))->format('H:i:s') }}</strong></span>
                            <span class="border-l border-school-line pl-4">Token <strong class="text-school-ink" x-text="qr.id ? `#${String(qr.id).padStart(4, '0')}` : '#—'">#{{ str_pad((string) ($activeQr['id'] ?? 0), 4, '0', STR_PAD_LEFT) }}</strong></span>
                        </div>
                    </div>

                    <div class="flex flex-col justify-between bg-white px-6 py-8 sm:px-10 sm:py-12">
                        <div>
                            <div class="mb-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-school-purple">{{ $school->name }}</div>
                            <h2 id="monitor-title" class="school-display max-w-md text-3xl font-semibold leading-tight sm:text-4xl">Pindai untuk mencatat kehadiran</h2>
                            <p class="mt-4 max-w-md text-base leading-7 text-school-muted">Buka aplikasi sekolah, aktifkan GPS, lalu arahkan kamera ke QR.</p>
                        </div>

                        <div class="my-10 flex items-center gap-4" aria-live="polite">
                            <div class="countdown-ring relative grid h-24 w-24 shrink-0 place-items-center rounded-full" :style="`--progress: ${progress()}`">
                                <span class="relative z-10 school-display text-3xl font-semibold tabular-nums" x-text="renderSeconds()">{{ str_pad((string) ($activeQr['countdown_seconds'] ?? 0), 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div>
                                <div class="text-xs text-school-soft">QR diperbarui dalam</div>
                                <div class="school-display text-xl font-semibold"><span x-text="seconds === 1 ? '1 detik' : `${renderSeconds()} detik`">{{ $activeQr['countdown_seconds'] ?? 0 }} detik</span></div>
                                <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-school-success"><i class="ti ti-clock-check" aria-hidden="true"></i>Waktu server aktif</div>
                            </div>
                        </div>

                        <x-monitor.attendance-summary :summary="$summary" />
                    </div>
                </div>
            </template>
        </section>
    </div>
@endsection

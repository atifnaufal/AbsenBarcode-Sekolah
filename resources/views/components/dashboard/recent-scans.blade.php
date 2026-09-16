@props(['scans', 'school'])

@php
    use App\Support\AttendancePresenter;
@endphp

<div class="school-offset-panel">
    <section class="relative rounded-school-card border border-school-line bg-white p-5">
        <div class="mb-5 flex items-start justify-between gap-4">
            <div>
                <h3 class="school-display text-base font-semibold">Pemindaian terbaru</h3>
                <p class="mt-1 text-xs text-school-soft">Aktivitas masuk pada layar QR hari ini</p>
            </div>
            <span class="shrink-0 text-xs font-semibold text-school-action">{{ $scans->count() }} terbaru</span>
        </div>

        @if ($scans->isEmpty())
            <div class="rounded-school-control border border-dashed border-school-line bg-school-canvas px-4 py-8 text-center">
                <i class="ti ti-scan text-2xl text-school-purple" aria-hidden="true"></i>
                <p class="mt-3 text-sm font-semibold text-school-ink">Belum ada pemindaian hari ini.</p>
                <p class="mt-1 text-xs text-school-muted">Data akan muncul setelah QR pertama dipindai.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-xs">
                    <caption class="sr-only">Daftar pemindaian terbaru</caption>
                    <thead class="border-b border-[#edf0f5] text-[10px] font-semibold uppercase tracking-[0.08em] text-[#9aa4b5]">
                    <tr>
                        <th scope="col" class="pb-2 font-semibold">Nama</th>
                        <th scope="col" class="pb-2 font-semibold">ID / kelas</th>
                        <th scope="col" class="pb-2 font-semibold">Waktu</th>
                        <th scope="col" class="pb-2 font-semibold">Hasil validasi</th>
                        <th scope="col" class="pb-2 font-semibold text-right">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf0f5]">
                    @foreach ($scans as $scan)
                        @php
                            $result = $scan->result?->value ?? 'unavailable';
                            $resultClass = match ($result) {
                                'success' => 'text-school-success',
                                'outside_area' => 'text-school-danger',
                                'expired' => 'text-school-warning',
                                'late' => 'text-orange-500',
                                default => 'text-school-muted',
                            };
                        @endphp
                        <tr class="hover:bg-school-canvas/60 group">
                            <td class="py-3 font-semibold">{{ $scan->user->name }}</td>
                            <td class="py-3 text-school-muted">{{ $scan->user->identifier }} · {{ $scan->user->class_name ?? 'Staf' }}</td>
                            <td class="py-3 tabular-nums text-school-muted">{{ AttendancePresenter::time($scan->scanned_at, $school->timezone) }}</td>
                            <td class="py-3">
                                <span class="inline-flex items-center gap-1.5 font-bold {{ $resultClass }}">
                                    {{ AttendancePresenter::result($scan->result) }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="inline-flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{-- Quick Toggle to Success if it was failed --}}
                                    @if($scan->result->value !== 'success')
                                    <form action="{{ route('admin.attendances.update', $scan) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="result" value="success">
                                        <button type="submit" class="text-[10px] font-black uppercase text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">Sahkan</button>
                                    </form>
                                    @endif

                                    <form action="{{ route('admin.attendances.destroy', $scan) }}" method="POST" onsubmit="return confirm('Hapus permanen log ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-black uppercase text-red-600 bg-red-50 px-2 py-1 rounded-md border border-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

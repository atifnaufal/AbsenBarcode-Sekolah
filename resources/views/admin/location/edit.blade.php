@extends('layouts.app')

@section('content')
{{-- Include Leaflet Asset Map Library via CDN --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-[1280px] mx-auto px-4 py-8">
    <div class="mb-10 animate-[fadeIn_.6s_ease]">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-[#0f1e3d] text-white flex items-center justify-center text-3xl shadow-xl shadow-[#0f1e3d]/20 border border-white/10">
                <i class="ti ti-settings-automation"></i>
            </div>
            <div>
                <h1 class="font-display text-3xl font-black text-[#0f1e3d] tracking-tight">Aktivasi Pengaturan</h1>
                <p class="text-sm text-[#68748b] font-medium uppercase tracking-widest">Sistem Konfigurasi Lokasi & Sesi</p>
            </div>
        </div>
    </div>

    {{-- Smart Search Console --}}
    <div class="mb-10 bg-white rounded-[40px] border border-school-line p-8 shadow-sm animate-[slideIn_.4s_ease-out] relative overflow-hidden group">
        <div class="absolute -right-24 -top-24 h-64 w-64 bg-blue-50 rounded-full blur-3xl opacity-50 group-hover:scale-110 transition-transform duration-700"></div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-end gap-6">
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-2 mb-1.5 pl-1">
                        <div class="h-5 w-5 rounded-lg bg-blue-100 text-[#2c68f5] flex items-center justify-center text-xs"><i class="ti ti-brand-google-maps"></i></div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#2c68f5]">Cari Lokasi / Salin Link Google Maps</label>
                    </div>
                    <div class="relative group/search">
                        <i class="ti ti-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within/search:text-[#2c68f5] transition-colors"></i>
                        <input type="text" id="smartSearch" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-4 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all placeholder:text-slate-400" placeholder="Paste link maps (ex: https://maps.app.goo.gl/...) atau ketik alamat lengkap">
                    </div>
                </div>
                <button type="button" id="btnSmartSearch" class="h-14 px-8 rounded-2xl bg-[#0f1e3d] text-white text-xs font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-slate-900/20 active:scale-95 flex items-center justify-center gap-3">
                    <i class="ti ti-wand text-lg text-[#ffd500]"></i>
                    <span>Analisis & Generate</span>
                </button>
            </div>
            <div id="searchFeedback" class="mt-4 hidden animate-[fadeIn_.3s_ease]">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100">
                    <i class="ti ti-circle-check"></i>
                    <span id="feedbackText">Koordinat ditemukan & diperbarui secara otomatis.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin Guidance Suite --}}
    <div class="mb-8 grid grid-cols-1 lg:grid-cols-3 gap-6 animate-[slideIn_.5s_ease-out]">
        <div class="bg-indigo-600 rounded-[32px] p-6 text-white shadow-xl shadow-indigo-500/20 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:rotate-12 transition-transform duration-500"><i class="ti ti-hand-finger text-8xl"></i></div>
            <div class="relative z-10">
                <div class="h-10 w-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl mb-4">
                    <i class="ti ti-info-circle"></i>
                </div>
                <h4 class="text-sm font-black uppercase tracking-widest mb-1">Peta Presisi</h4>
                <p class="text-[11px] font-medium text-indigo-50 leading-relaxed">
                    Geser <b>Marker Biru</b> pada peta di sebelah kanan untuk mendapatkan koordinat yang presisi secara otomatis.
                </p>
            </div>
        </div>
        <div class="bg-emerald-600 rounded-[32px] p-6 text-white shadow-xl shadow-emerald-500/20 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:rotate-12 transition-transform duration-500"><i class="ti ti-clock text-8xl"></i></div>
            <div class="relative z-10">
                <div class="h-10 w-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl mb-4">
                    <i class="ti ti-shield-lock"></i>
                </div>
                <h4 class="text-sm font-black uppercase tracking-widest mb-1">Jam Operasional</h4>
                <p class="text-[11px] font-medium text-emerald-50 leading-relaxed">
                    QR Code hanya akan aktif di layar monitor pada rentang waktu yang Anda tentukan di form jadwal bawah.
                </p>
            </div>
        </div>
        <div class="bg-blue-600 rounded-[32px] p-6 text-white shadow-xl shadow-blue-500/20 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:rotate-12 transition-transform duration-500"><i class="ti ti-map-pin text-8xl"></i></div>
            <div class="relative z-10">
                <div class="h-10 w-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl mb-4">
                    <i class="ti ti-ruler-2"></i>
                </div>
                <h4 class="text-sm font-black uppercase tracking-widest mb-1">Radius Aman</h4>
                <p class="text-[11px] font-medium text-blue-50 leading-relaxed">
                    Tentukan jangkauan maksimal siswa. Disarankan <b>80-100 meter</b> untuk akurasi terbaik di lapangan.
                </p>
            </div>
        </div>
    </div>

    @if(session('ok'))
    <div class="mb-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 px-5 py-3 text-sm text-emerald-800 font-medium flex items-center gap-3 animate-[slideIn_.3s_ease-out]">
        <i class="ti ti-circle-check text-emerald-600 text-lg"></i>
        {{ session('ok') }}
    </div>
    @endif

    <div class="grid gap-8 lg:grid-cols-2 xl:grid-cols-[1fr_500px]">
        {{-- Configuration Console --}}
        <div class="relative overflow-hidden rounded-[40px] bg-white border border-school-line shadow-sm p-8 sm:p-10 animate-[fadeIn_.8s_ease]">
            <form method="POST" action="{{ route('admin.location.update') }}" class="space-y-8" id="locationForm">
                @csrf @method('PUT')

                <div class="space-y-6">
                    <h3 class="text-xs font-black text-[#623ed8] uppercase tracking-[0.2em] flex items-center gap-2">
                        <i class="ti ti-id-badge text-base"></i> Identitas & Geofencing
                    </h3>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 pl-1">Nama Instansi Pendidikan</label>
                        <input name="name" id="inputName" value="{{ old('name', $school->name) }}" required class="w-full h-14 rounded-2xl border border-school-line bg-slate-50/50 px-5 text-sm font-bold text-[#0f1e3d] focus:border-[#2c68f5] focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 pl-1">Garis Lintang (Latitude)</label>
                            <input name="latitude" id="inputLat" type="number" step="0.0000001" value="{{ old('latitude', $school->latitude) }}" required class="w-full h-14 rounded-2xl border border-school-line bg-slate-50/50 px-5 text-sm font-mono font-bold focus:border-[#2c68f5] focus:bg-white outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 pl-1">Garis Bujur (Longitude)</label>
                            <input name="longitude" id="inputLng" type="number" step="0.0000001" value="{{ old('longitude', $school->longitude) }}" required class="w-full h-14 rounded-2xl border border-school-line bg-slate-50/50 px-5 text-sm font-mono font-bold focus:border-[#2c68f5] focus:bg-white outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 pl-1">Radius Keamanan (Meter)</label>
                        <div class="relative">
                            <input name="radius_meters" id="inputRadius" type="number" value="{{ old('radius_meters', $school->radius_meters) }}" required class="w-full h-14 rounded-2xl border border-school-line bg-slate-50/50 px-5 text-sm font-black focus:border-[#2c68f5] focus:bg-white outline-none transition-all">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 uppercase">Distance Units</div>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-100"></div>

                <div class="space-y-6">
                    <h3 class="text-xs font-black text-emerald-600 uppercase tracking-[0.2em] flex items-center gap-2">
                        <i class="ti ti-clock-bolt text-base"></i> Jadwal & Sesi QR
                    </h3>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 pl-1">Keterangan Sesi</label>
                        <input name="attendance_label" value="{{ old('attendance_label', $school->attendance_label) }}" class="w-full h-14 rounded-2xl border border-school-line bg-slate-50/50 px-5 text-sm font-bold text-[#0f1e3d] focus:border-emerald-500 focus:bg-white outline-none transition-all" placeholder="Contoh: Absen Pagi">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 pl-1">Jam Aktif</label>
                            <input name="attendance_start" type="time" value="{{ old('attendance_start', $school->attendance_start ? substr($school->attendance_start, 0, 5) : '') }}" class="w-full h-14 rounded-2xl border border-school-line bg-slate-50/50 px-5 text-sm font-bold focus:border-emerald-500 focus:bg-white outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 pl-1">Jam Berakhir</label>
                            <input name="attendance_end" type="time" value="{{ old('attendance_end', $school->attendance_end ? substr($school->attendance_end, 0, 5) : '') }}" class="w-full h-14 rounded-2xl border border-school-line bg-slate-50/50 px-5 text-sm font-bold focus:border-emerald-500 focus:bg-white outline-none transition-all">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full h-16 rounded-[24px] bg-[#0f1e3d] text-white font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-[#0f1e3d]/30 hover:bg-black hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-3">
                    <i class="ti ti-device-floppy text-xl"></i> Simpan Konfigurasi
                </button>
            </form>
        </div>

        {{-- Live Preview Maps Panel --}}
        <div class="rounded-[40px] border border-school-line bg-white shadow-xl shadow-slate-200/50 overflow-hidden flex flex-col animate-[fadeIn_.8s_ease]">
            <div class="p-8 border-b border-school-line bg-slate-50/50">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#2c68f5]">Visualisasi Radar</p>
                <h3 class="font-display text-xl font-black text-[#0f1e3d] mt-1">Pratinjau Geofence</h3>
            </div>

            <div class="p-6 flex-1 bg-white">
                <div id="schoolLiveMap" class="w-full h-[400px] lg:h-full min-h-[400px] rounded-[32px] border border-school-line shadow-inner relative z-10 bg-slate-100 overflow-hidden"></div>
            </div>

            <div class="p-8 bg-slate-50/80 border-t border-school-line space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="ti ti-gps"></i></div>
                        <span class="text-xs font-black uppercase tracking-widest text-slate-500">Status Satelit</span>
                    </div>
                    <span class="text-[10px] font-black px-3 py-1 bg-emerald-500 text-white rounded-full shadow-lg shadow-emerald-500/20">AKTIF</span>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-blue-100 text-[#2c68f5] flex items-center justify-center"><i class="ti ti-ruler-measure"></i></div>
                        <span class="text-xs font-black uppercase tracking-widest text-slate-500">Cakupan Area</span>
                    </div>
                    <span class="text-sm font-black text-[#0f1e3d]" id="lblRadius">{{ $school->radius_meters }} Meter</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let lat = parseFloat(document.getElementById('inputLat').value) || -6.9182000;
        let lng = parseFloat(document.getElementById('inputLng').value) || 110.2056000;
        let radius = parseInt(document.getElementById('inputRadius').value) || 80;

        const map = L.map('schoolLiveMap').setView([lat, lng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let schoolMarker = L.marker([lat, lng], { draggable: true }).addTo(map);
        let geofenceCircle = L.circle([lat, lng], {
            color: '#2c68f5',
            fillColor: '#2c68f5',
            fillOpacity: 0.1,
            weight: 2,
            radius: radius
        }).addTo(map);

        schoolMarker.on('dragend', function (e) {
            let position = schoolMarker.getLatLng();
            document.getElementById('inputLat').value = position.lat.toFixed(7);
            document.getElementById('inputLng').value = position.lng.toFixed(7);
            geofenceCircle.setLatLng(position);
            map.panTo(position);
        });

        const syncMapFromInputs = () => {
            let nLat = parseFloat(document.getElementById('inputLat').value) || lat;
            let nLng = parseFloat(document.getElementById('inputLng').value) || lng;
            let nRadius = parseInt(document.getElementById('inputRadius').value) || radius;

            let nPos = [nLat, nLng];
            schoolMarker.setLatLng(nPos);
            geofenceCircle.setLatLng(nPos);
            geofenceCircle.setRadius(nRadius);
            document.getElementById('lblRadius').innerText = nRadius + ' Meter';
            map.panTo(nPos);
        };

        ['inputLat', 'inputLng', 'inputRadius'].forEach(id => {
            document.getElementById(id).addEventListener('input', syncMapFromInputs);
        });

        // 🧠 SMART SEARCH & GENERATE LOGIC
        const btnSearch = document.getElementById('btnSmartSearch');
        const inputSearch = document.getElementById('smartSearch');
        const feedback = document.getElementById('searchFeedback');
        const feedbackText = document.getElementById('feedbackText');

        btnSearch.addEventListener('click', async () => {
            const query = inputSearch.value.trim();
            if (!query) return;

            btnSearch.disabled = true;
            btnSearch.innerHTML = '<i class="ti ti-loader-2 animate-spin"></i> Analyzing...';

            try {
                let nLat, nLng;

                // 1. Check for Google Maps URL pattern (@lat,lng)
                const urlCoordsMatch = query.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
                const qCoordsMatch = query.match(/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/);

                if (urlCoordsMatch) {
                    nLat = parseFloat(urlCoordsMatch[1]);
                    nLng = parseFloat(urlCoordsMatch[2]);
                } else if (qCoordsMatch) {
                    nLat = parseFloat(qCoordsMatch[1]);
                    nLng = parseFloat(qCoordsMatch[2]);
                } else {
                    // 2. Geocoding using Nominatim (OpenStreetMap)
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`);
                    const data = await response.json();

                    if (data && data.length > 0) {
                        nLat = parseFloat(data[0].lat);
                        nLng = parseFloat(data[0].lon);
                    }
                }

                if (nLat && nLng) {
                    document.getElementById('inputLat').value = nLat.toFixed(7);
                    document.getElementById('inputLng').value = nLng.toFixed(7);

                    const newPos = [nLat, nLng];
                    schoolMarker.setLatLng(newPos);
                    geofenceCircle.setLatLng(newPos);
                    map.setView(newPos, 17);

                    feedback.classList.remove('hidden');
                    feedback.querySelector('div').className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100";
                    feedbackText.innerText = "Lokasi ditemukan & koordinat berhasil sinkron!";
                } else {
                    throw new Error("Lokasi tidak dikenali.");
                }

            } catch (error) {
                feedback.classList.remove('hidden');
                feedback.querySelector('div').className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-[10px] font-bold border border-red-100";
                feedbackText.innerText = "Gagal mendeteksi lokasi. Pastikan link/alamat benar.";
            } finally {
                btnSearch.disabled = false;
                btnSearch.innerHTML = '<i class="ti ti-wand text-lg text-[#ffd500]"></i> <span>Analisis & Generate</span>';
                setTimeout(() => feedback.classList.add('hidden'), 5000);
            }
        });
    });
</script>
@endsection

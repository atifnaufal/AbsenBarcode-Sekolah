@extends('layouts.app')

@section('content')
{{-- Include Leaflet Asset Map Library via CDN --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-[1280px] mx-auto px-2 sm:px-4">
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-gradient-to-tr from-[#0f1e3d] to-[#2c68f5] flex items-center justify-center text-white text-xl shadow-md">
                <i class="ti ti-map-2"></i>
            </div>
            <div>
                <h1 class="font-display text-2xl font-black text-[#0f1e3d]">Konfigurasi Geofencing Sekolah</h1>
                <p class="text-xs text-[#68748b]">Atur koordinat pusat instansi dan batasan radius pemindaian absensi</p>
            </div>
        </div>
    </div>

    @if(session('ok'))
    <div class="mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 text-sm text-emerald-800 font-medium flex items-center justify-between shadow-sm">
        <span class="flex items-center gap-2">
            <i class="ti ti-circle-check text-emerald-600 text-lg"></i>
            {{ session('ok') }}
        </span>
        <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="ti ti-x"></i></button>
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_400px]">
        {{-- Form Configuration Card --}}
        <div class="relative overflow-hidden rounded-3xl bg-white border border-school-line shadow-sm p-6 sm:p-8">
            <div class="absolute -top-4 -right-4 h-24 w-24 rounded-full bg-gradient-to-br from-[#2c68f5]/10 to-[#623ed8]/10 blur-xl"></div>

            <form method="POST" action="{{ route('admin.location.update') }}" class="space-y-5" id="locationForm">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-[#0f1e3d] block mb-1.5">Nama Instansi / Sekolah</label>
                    <input name="name" id="inputName" value="{{ old('name', $school->name) }}" required class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-semibold text-[#172033] focus:border-[#2c68f5] focus:outline-none transition">
                </div>

                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-[#0f1e3d] block mb-1.5">Alamat Jalan Lengkap</label>
                    <input name="address" id="inputAddress" value="{{ old('address', $school->address) }}" class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm text-[#172033] focus:border-[#2c68f5] focus:outline-none transition" placeholder="Jl. Raya Utama No. 123, Kendal">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-[#0f1e3d] block mb-1.5">Titik Latitude</label>
                        <input name="latitude" id="inputLat" type="number" step="0.0000001" value="{{ old('latitude', $school->latitude) }}" required class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-mono focus:border-[#2c68f5] focus:outline-none transition" placeholder="-6.9182000">
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-[#0f1e3d] block mb-1.5">Titik Longitude</label>
                        <input name="longitude" id="inputLng" type="number" step="0.0000001" value="{{ old('longitude', $school->longitude) }}" required class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-mono focus:border-[#2c68f5] focus:outline-none transition" placeholder="110.2056000">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-[#0f1e3d] block mb-1.5">Radius Batasan Kehadiran (meter)</label>
                    <input name="radius_meters" id="inputRadius" type="number" min="10" max="5000" value="{{ old('radius_meters', $school->radius_meters) }}" required class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-bold focus:border-[#2c68f5] focus:outline-none transition" placeholder="80">
                    <p class="mt-1 text-[11px] text-[#8a95a8] leading-relaxed">Jarak radius sirkular aman dari titik koordinat pusat sekolah bagi siswa/guru (direkomendasikan: 50m - 150m).</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full h-11 rounded-xl bg-gradient-to-r from-[#0f1e3d] to-[#2c68f5] text-white font-bold text-xs shadow-lg shadow-[#0f1e3d]/20 hover:opacity-95 transition flex items-center justify-center gap-2">
                        <i class="ti ti-device-floppy text-sm"></i> Simpan Parameter Geofencing
                    </button>
                </div>
            </form>

            <hr class="my-8 border-school-line">

            {{-- NEW: QR Schedule Config --}}
            <form method="POST" action="{{ route('admin.location.update') }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="bg-[#f8f9fc] p-5 rounded-2xl border border-school-line">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="ti ti-clock-bolt text-lg text-[#623ed8]"></i>
                        <h3 class="text-sm font-bold text-[#0f1e3d]">Pengaturan Jadwal QR</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1">Keterangan Sesi</label>
                            <input name="attendance_label" value="{{ old('attendance_label', $school->attendance_label) }}" class="w-full h-10 rounded-lg border border-school-line px-3 text-xs focus:border-[#2c68f5] outline-none" placeholder="Contoh: Absen Masuk Sekolah">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1">Jam Mulai</label>
                                <input name="attendance_start" type="time" value="{{ old('attendance_start', $school->attendance_start ? substr($school->attendance_start, 0, 5) : '') }}" class="w-full h-10 rounded-lg border border-school-line px-3 text-xs focus:border-[#2c68f5] outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1">Jam Berakhir</label>
                                <input name="attendance_end" type="time" value="{{ old('attendance_end', $school->attendance_end ? substr($school->attendance_end, 0, 5) : '') }}" class="w-full h-10 rounded-lg border border-school-line px-3 text-xs focus:border-[#2c68f5] outline-none">
                            </div>
                        </div>
                    </div>

                    <p class="mt-4 text-[10px] text-[#8a95a8] italic">QR Code hanya akan tampil pada monitor dan bisa dipindai dalam rentang waktu di atas.</p>

                    <button type="submit" class="mt-4 w-full h-10 rounded-xl border border-[#623ed8] text-[#623ed8] font-bold text-[11px] hover:bg-[#623ed8] hover:text-white transition flex items-center justify-center gap-2">
                        Update Jadwal QR
                    </button>
                </div>
            </form>
        </div>

        {{-- Live Render Maps Panel --}}
        <div class="rounded-3xl border border-school-line bg-white shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-school-line bg-[#f8f9fc]">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#623ed8]">Pratinjau Live Peta</p>
                <h3 class="font-display text-base font-bold text-[#0f1e3d] mt-0.5">Visualisasi Radius Maps</h3>
            </div>

            {{-- Map Element Container --}}
            <div class="p-4 flex-1">
                <div id="schoolLiveMap" class="w-full h-[260px] rounded-2xl border border-school-line shadow-inner relative z-10 bg-slate-100"></div>
            </div>

            <div class="p-5 bg-[#f8f9fc] border-t border-school-line space-y-3">
                <div class="flex items-center justify-between text-xs font-medium">
                    <span class="text-[#8a95a8]">Status GPS</span>
                    <span class="font-bold text-emerald-600 flex items-center gap-1"><i class="ti ti-circle-check"></i> Geofence Aktif</span>
                </div>
                <div class="flex items-center justify-between text-xs font-medium border-t border-school-line pt-2.5">
                    <span class="text-[#8a95a8]">Radius Terkunci</span>
                    <span class="font-bold text-[#0f1e3d]" id="lblRadius">{{ $school->radius_meters }} Meter</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Fetch current values
        let lat = parseFloat(document.getElementById('inputLat').value) || -6.9182000;
        let lng = parseFloat(document.getElementById('inputLng').value) || 110.2056000;
        let radius = parseInt(document.getElementById('inputRadius').value) || 80;

        // Initialize live leaflet map view
        const map = L.map('schoolLiveMap').setView([lat, lng], 16);

        // Add high fidelity OpenStreetMap tile layer tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Append precision school marker pin
        let schoolMarker = L.marker([lat, lng], { draggable: true }).addTo(map);

        // Append bounding circular geofencing zone
        let geofenceCircle = L.circle([lat, lng], {
            color: '#2c68f5',
            fillColor: '#2c68f5',
            fillOpacity: 0.15,
            radius: radius
        }).addTo(map);

        // Sync marker drag to inputs
        schoolMarker.on('dragend', function (e) {
            let position = schoolMarker.getLatLng();
            document.getElementById('inputLat').value = position.lat.toFixed(7);
            document.getElementById('inputLng').value = position.lng.toFixed(7);
            geofenceCircle.setLatLng(position);
            map.panTo(position);
        });

        // Add real-time event change listeners to form fields for premium dynamic UI updates
        const syncMapFromInputs = () => {
            let nLat = parseFloat(document.getElementById('inputLat').value) || lat;
            let nLng = parseFloat(document.getElementById('inputLng').value) || lng;
            let nRadius = parseInt(document.getElementById('inputRadius').value) || radius;

            let nPos = [nLat, nLng];
            schoolMarker.setLatLng(nPos);
            geofenceCircle.setLatLng(nPos);
            geofenceCircle.setRadius(nRadius);
            document.getElementById('lblRadius').innerText = nRadius + ' Meter';
            map.setView(nPos);
        };

        document.getElementById('inputLat').addEventListener('input', syncMapFromInputs);
        document.getElementById('inputLng').addEventListener('input', syncMapFromInputs);
        document.getElementById('inputRadius').addEventListener('input', syncMapFromInputs);
    });
</script>
@endsection

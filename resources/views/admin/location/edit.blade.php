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

    @if(session('error') || $errors->any())
    <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-800 font-medium shadow-sm">
        <div class="flex items-start gap-2">
            <i class="ti ti-alert-circle text-red-600 text-lg mt-0.5"></i>
            <div class="flex-1">
                <p class="font-bold">Konfigurasi Gagal Disimpan:</p>
                <ul class="mt-1 list-disc list-inside text-xs space-y-1">
                    @if(session('error'))
                        <li>{{ session('error') }}</li>
                    @endif
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="$el.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700"><i class="ti ti-x"></i></button>
        </div>
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_400px]">
        {{-- Form Configuration Card --}}
        <div class="relative overflow-hidden rounded-3xl bg-white border border-school-line shadow-sm p-6 sm:p-8">
            <div class="absolute -top-4 -right-4 h-24 w-24 rounded-full bg-gradient-to-br from-[#2c68f5]/10 to-[#623ed8]/10 blur-xl"></div>

            <form method="POST" action="{{ route('admin.location.update') }}" class="space-y-6" id="locationForm">
                @csrf
                @method('PUT')

                {{-- Bagian 1: Identitas & Geofencing --}}
                <div class="space-y-5">
                    <h3 class="text-sm font-black text-[#623ed8] uppercase tracking-wider flex items-center gap-2">
                        <i class="ti ti-map-pin-check"></i> Parameter Geofencing
                    </h3>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1.5">Nama Instansi</label>
                        <input name="name" id="inputName" value="{{ old('name', $school->name) }}" required class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-semibold text-[#172033] focus:border-[#2c68f5] focus:outline-none transition">
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1.5">Latitude</label>
                            <input name="latitude" id="inputLat" type="number" step="0.0000001" value="{{ old('latitude', $school->latitude) }}" required class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-mono focus:border-[#2c68f5] focus:outline-none transition">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1.5">Longitude</label>
                            <input name="longitude" id="inputLng" type="number" step="0.0000001" value="{{ old('longitude', $school->longitude) }}" required class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-mono focus:border-[#2c68f5] focus:outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1.5">Radius Aman (m)</label>
                        <input name="radius_meters" id="inputRadius" type="number" value="{{ old('radius_meters', $school->radius_meters) }}" required class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-bold focus:border-[#2c68f5] focus:outline-none transition">
                    </div>
                </div>

                <div class="h-px bg-slate-100 my-6"></div>

                {{-- Bagian 2: Jadwal Dinamis --}}
                <div class="space-y-5 bg-slate-50/50 p-5 rounded-2xl border border-dashed border-slate-200">
                    <h3 class="text-sm font-black text-emerald-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="ti ti-clock-bolt"></i> Jadwal & Agenda QR
                    </h3>

                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1.5">Keterangan / Nama Agenda</label>
                        <input name="attendance_label" value="{{ old('attendance_label', $school->attendance_label) }}" class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm font-bold text-[#0f1e3d] focus:border-emerald-500 focus:outline-none transition" placeholder="Contoh: Absen Upacara, Rapat Guru, dll">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1.5">Waktu Mulai</label>
                            <input name="attendance_start" type="time" value="{{ old('attendance_start', $school->attendance_start ? substr($school->attendance_start, 0, 5) : '') }}" class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm focus:border-emerald-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-[#68748b] block mb-1.5">Waktu Selesai</label>
                            <input name="attendance_end" type="time" value="{{ old('attendance_end', $school->attendance_end ? substr($school->attendance_end, 0, 5) : '') }}" class="w-full h-11 rounded-xl border border-school-line bg-white px-3.5 text-sm focus:border-emerald-500 focus:outline-none transition">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-14 rounded-2xl bg-gradient-to-r from-[#0f1e3d] to-[#2c68f5] text-white font-black text-sm shadow-xl shadow-[#0f1e3d]/20 hover:scale-[1.01] transition-all flex items-center justify-center gap-3">
                        <i class="ti ti-device-floppy text-lg"></i> SIMPAN PERUBAHAN CONFIG
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

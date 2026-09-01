@extends('layouts.app')

@section('title', 'Peta GIS Kerusakan Jalan - JALAN KU')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet-gesture-handling@1.2.2/dist/leaflet-gesture-handling.min.css" />
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Title & Filter Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-amber-600 uppercase tracking-widest">
                <i class="fa-solid fa-map-location-dot"></i>
                <span>Geographic Information System</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 mt-1">Peta Sebaran Kerusakan Jalan</h1>
            <p class="text-sm text-slate-600">Klik marker untuk melihat detail laporan, status perbaikan, dan foto kondisi jalan.</p>
        </div>

        <!-- Filter Controls -->
        <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-2.5">
            <div class="w-full sm:w-auto">
                <select id="filter-status" class="w-full sm:w-auto text-xs font-semibold bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-700 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="DIAJUKAN">Diajukan</option>
                    <option value="DIVERIFIKASI">Diverifikasi</option>
                    <option value="DITUGASKAN">Ditugaskan</option>
                    <option value="SURVEI">Survei</option>
                    <option value="SEDANG DIPERBAIKI">Sedang Diperbaiki</option>
                    <option value="SELESAI">Selesai</option>
                </select>
            </div>

            <div class="w-full sm:w-auto">
                <select id="filter-kecamatan" class="w-full sm:w-auto text-xs font-semibold bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-700 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">Semua Wilayah / Kecamatan</option>
                    @foreach($kecamatanList as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-auto">
                <select id="filter-damage" class="w-full sm:w-auto text-xs font-semibold bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-700 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">Semua Jenis Kerusakan</option>
                    <option value="pothole">Lubang Jalan (Pothole)</option>
                    <option value="crack">Retak Jalan (Crack)</option>
                    <option value="landslide">Longsor / Amblas (Landslide)</option>
                    <option value="lainnya">Kerusakan Lainnya</option>
                </select>
            </div>

            <button id="btn-reset" class="text-xs px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                <i class="fa-solid fa-rotate-right mr-1"></i> Reset
            </button>
        </div>
    </div>

    <!-- Map Container -->
    <div class="relative bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-200">
        <div id="full-map" class="w-full h-[450px] sm:h-[550px] lg:h-[650px] z-10"></div>

        <!-- Floating Legend -->
        <div class="absolute bottom-4 left-4 z-20 bg-navy-950/90 text-white p-3.5 sm:p-4 rounded-2xl border border-slate-700/80 shadow-2xl backdrop-blur-md max-w-[220px] sm:max-w-xs text-xs space-y-2">
            <h4 class="font-bold text-slate-200 flex items-center">
                <i class="fa-solid fa-layer-group text-amber-400 mr-2"></i> Status Penanganan
            </h4>
            <div class="space-y-1.5 font-medium text-[11px] sm:text-xs">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-red-500 shrink-0"></span>
                    <span>Sangat Prioritas (Merah)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-orange-500 shrink-0"></span>
                    <span>Prioritas Tinggi (Oranye)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-500 shrink-0"></span>
                    <span>Sedang (Kuning)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500 shrink-0"></span>
                    <span>Rendah / Info (Biru)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                    <span>Selesai Diperbaiki (Hijau)</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet-gesture-handling@1.2.2/dist/leaflet-gesture-handling.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var defaultLat = {{ (float) \App\Models\SystemSetting::get('default_map_lat', -6.9200) }};
        var defaultLng = {{ (float) \App\Models\SystemSetting::get('default_map_lng', 107.6250) }};

        var map = L.map('full-map', {
            zoomControl: true,
            scrollWheelZoom: true,
            gestureHandling: true
        }).setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var markersLayer = L.layerGroup().addTo(map);

        function loadReports() {
            var status = document.getElementById('filter-status').value;
            var kec = document.getElementById('filter-kecamatan').value;
            var damage = document.getElementById('filter-damage').value;

            var params = new URLSearchParams();
            if (status) params.append('status', status);
            if (kec) params.append('kecamatan', kec);
            if (damage) params.append('damage_type', damage);

            fetch("{{ route('api.geo-reports') }}?" + params.toString())
                .then(res => res.json())
                .then(data => {
                    markersLayer.clearLayers();
                    var bounds = [];

                    if (data.reports && data.reports.length > 0) {
                        data.reports.forEach(function(r) {
                            var color = r.marker_color || '#3b82f6';
                            var marker = L.circleMarker([r.latitude, r.longitude], {
                                radius: 9,
                                fillColor: color,
                                color: '#ffffff',
                                weight: 2.5,
                                opacity: 1,
                                fillOpacity: 0.95
                            });

                            var popupContent = `
                                <div class="p-3 font-sans space-y-2" style="min-width: 220px; max-width: 260px;">
                                    <div class="relative h-28 bg-slate-900 rounded-lg overflow-hidden mb-2">
                                        <img src="${r.photo_url}" class="w-full h-full object-cover">
                                        <span class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-navy-950/80 text-amber-300">${r.ticket_number}</span>
                                    </div>
                                    <h3 class="font-bold text-sm text-navy-950 leading-tight">${r.road_name}</h3>
                                    <div class="text-xs space-y-1 text-slate-600">
                                        <p><strong>Status:</strong> <span class="text-navy-900 font-semibold">${r.status}</span></p>
                                        <p><strong>Jenis Cacat:</strong> ${r.damage_type} (${r.disturbance_level})</p>
                                        <p><strong>Progres:</strong> <span class="text-emerald-600 font-bold">${r.progress}%</span></p>
                                        <p><strong>Prioritas:</strong> ${r.priority_level}</p>
                                    </div>
                                    <a href="${r.detail_url}" class="block text-center mt-2 w-full py-1.5 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-lg transition">
                                        Lihat Detail Laporan &rarr;
                                    </a>
                                </div>
                            `;

                            marker.bindPopup(popupContent);
                            markersLayer.addLayer(marker);
                            bounds.push([r.latitude, r.longitude]);
                        });

                        if (bounds.length > 0) {
                            map.fitBounds(bounds, { padding: [40, 40] });
                        }
                    }
                })
                .catch(err => console.error('Error fetching map markers:', err));
        }

        document.getElementById('filter-status').addEventListener('change', loadReports);
        document.getElementById('filter-kecamatan').addEventListener('change', loadReports);
        document.getElementById('filter-damage').addEventListener('change', loadReports);
        document.getElementById('btn-reset').addEventListener('click', function() {
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-kecamatan').value = '';
            document.getElementById('filter-damage').value = '';
            loadReports();
        });

        loadReports();

        setTimeout(function() {
            map.invalidateSize();
        }, 400);
    });
</script>
@endpush

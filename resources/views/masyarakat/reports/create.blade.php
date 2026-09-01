@extends('layouts.app')

@section('title', 'Form Pengaduan Kerusakan Jalan - JALAN KU')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet-gesture-handling@1.2.2/dist/leaflet-gesture-handling.min.css" />
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Header -->
    <div class="mb-8 space-y-2">
        <a href="{{ route('masyarakat.dashboard') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-500 hover:text-amber-600 mb-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
        <h1 class="text-3xl font-extrabold text-navy-900 tracking-tight">Form Pengaduan Kerusakan Jalan</h1>
        <p class="text-sm text-slate-600">Lengkapi data kerusakan jalan dan lampirkan maksimal 3 foto kondisi awal beserta titik koordinat GPS.</p>
    </div>

    <!-- Main Card Form -->
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-xl">
        <form method="POST" action="{{ route('masyarakat.reports.store') }}" enctype="multipart/form-data" id="report-form" class="space-y-8">
            @csrf

            <!-- Section 1: Informasi Dasar Laporan -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-navy-900 flex items-center border-b border-slate-100 pb-3">
                    <span class="w-6 h-6 rounded-full bg-navy-900 text-amber-400 text-xs flex items-center justify-center font-bold mr-2">1</span>
                    Informasi Pengaduan
                </h3>

                <!-- Judul Laporan -->
                <div class="space-y-1">
                    <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Judul Pengaduan <span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Aspal Rusak Berlubang Parah di Dekat RSUD" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Deskripsi Kerusakan -->
                <div class="space-y-1">
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Deskripsi Kerusakan & Kondisi Lapangan <span class="text-rose-500">*</span></label>
                    <textarea id="description" name="description" rows="3" required placeholder="Jelaskan detail kerusakan, kedalaman lubang, atau potensi bahaya kecelakaan..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Jenis Kerusakan -->
                    <div class="space-y-1">
                        <label for="damage_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Jenis Kerusakan <span class="text-rose-500">*</span></label>
                        <select id="damage_type" name="damage_type" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="pothole" {{ old('damage_type') == 'pothole' ? 'selected' : '' }}>Lubang Jalan (Pothole)</option>
                            <option value="crack" {{ old('damage_type') == 'crack' ? 'selected' : '' }}>Retak Jalan (Crack)</option>
                            <option value="landslide" {{ old('damage_type') == 'landslide' ? 'selected' : '' }}>Longsor / Amblas (Landslide)</option>
                            <option value="lainnya" {{ old('damage_type') == 'lainnya' ? 'selected' : '' }}>Kerusakan Lainnya</option>
                        </select>
                    </div>

                    <!-- Tingkat Gangguan -->
                    <div class="space-y-1">
                        <label for="disturbance_level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tingkat Gangguan / Bahaya <span class="text-rose-500">*</span></label>
                        <select id="disturbance_level" name="disturbance_level" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="sangat_parah" {{ old('disturbance_level') == 'sangat_parah' ? 'selected' : '' }}>Sangat Parah (Macet Total / Rawan Kecelakaan Berat)</option>
                            <option value="tinggi" {{ old('disturbance_level') == 'tinggi' ? 'selected' : '' }}>Tinggi (Membahayakan Pengendara Motor)</option>
                            <option value="sedang" {{ old('disturbance_level', 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang (Mengganggu Kelancaran Lalu Lintas)</option>
                            <option value="rendah" {{ old('disturbance_level') == 'rendah' ? 'selected' : '' }}>Rendah (Kerusakan Awal / Ringan)</option>
                        </select>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="space-y-1">
                    <label for="additional_info" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Informasi Tambahan / Patokan Lokasi</label>
                    <input type="text" id="additional_info" name="additional_info" value="{{ old('additional_info') }}" placeholder="Contoh: Dekat tiang listrik depan gerbang sekolah, 200m dari simpang tiga" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Section 2: Lokasi & Peta GPS -->
            <div class="space-y-4 pt-4">
                <h3 class="text-base font-bold text-navy-900 flex items-center border-b border-slate-100 pb-3">
                    <span class="w-6 h-6 rounded-full bg-navy-900 text-amber-400 text-xs flex items-center justify-center font-bold mr-2">2</span>
                    Lokasi & Geotagging Presisi
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Nama Jalan -->
                    <div class="space-y-1">
                        <label for="road_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Ruas Jalan <span class="text-rose-500">*</span></label>
                        <input type="text" id="road_name" name="road_name" value="{{ old('road_name') }}" required placeholder="Contoh: Jalan Cikajang" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <!-- Kecamatan -->
                    <div class="space-y-1">
                        <label for="kecamatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kecamatan <span class="text-rose-500">*</span></label>
                        <input type="text" id="kecamatan" name="kecamatan" value="{{ old('kecamatan') }}" required placeholder="Nama Kecamatan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <!-- Desa -->
                    <div class="space-y-1">
                        <label for="desa" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Desa / Kelurahan <span class="text-rose-500">*</span></label>
                        <input type="text" id="desa" name="desa" value="{{ old('desa') }}" required placeholder="Nama Desa" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Detail Alamat -->
                <div class="space-y-1">
                    <label for="address_detail" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Lengkap / Keterangan Ruas</label>
                    <input type="text" id="address_detail" name="address_detail" value="{{ old('address_detail') }}" placeholder="Contoh: Jl. Cikajang KM 4.5 No. 40" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Interactive Leaflet Pin Picker -->
                <div class="space-y-2">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 text-xs">
                        <span class="font-bold text-slate-700 uppercase tracking-wider">Geser Pin Marker ke Titik Kerusakan:</span>
                        <button type="button" id="btn-detect-gps" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-xs rounded-xl shadow-lg shadow-amber-500/25 hover:scale-105 active:scale-95 transition animate-pulse hover:animate-none">
                            <i class="fa-solid fa-location-crosshairs text-sm"></i>
                            <span>Deteksi Lokasi GPS Saya</span>
                        </button>
                    </div>

                    <div id="gps-status-text" class="text-xs min-h-[20px] transition"></div>
                    <div id="picker-map" class="w-full h-64 rounded-2xl border border-slate-300 overflow-hidden shadow-inner z-10"></div>

                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="font-semibold text-slate-500">Latitude</label>
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', '-6.9200000') }}" readonly class="w-full px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg font-mono text-slate-700">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-500">Longitude</label>
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', '107.6250000') }}" readonly class="w-full px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg font-mono text-slate-700">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 16, 18, 38, 39, 40. UPLOAD FOTO KONDISI AWAL (Maksimal 3 Foto) -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-navy-900 flex items-center">
                        <span class="w-6 h-6 rounded-full bg-navy-900 text-amber-400 text-xs flex items-center justify-center font-bold mr-2">3</span>
                        Foto Kondisi Awal
                    </h3>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                        Maksimal 3 Foto
                    </span>
                </div>

                <!-- 16. Alert Box Warning -->
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start space-x-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5"></i>
                    <div>
                        <strong class="font-bold">Ketentuan Foto:</strong> Maksimal 3 foto untuk setiap laporan. Format yang diperbolehkan: JPG, JPEG, PNG, WEBP (Maksimal 5 MB per foto). Foto ke-4 akan ditolak otomatis.
                    </div>
                </div>

                <!-- Dropzone / Input File -->
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-amber-500 transition cursor-pointer bg-slate-50" id="drop-area">
                    <input type="file" id="photo-input" name="photos[]" multiple accept="image/jpeg,image/png,image/webp,image/jpg" class="hidden">
                    <div class="space-y-2 pointer-events-none">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto text-xl">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Klik atau seret foto ke area ini untuk mengunggah</p>
                        <p class="text-[11px] text-slate-400">Pilih 1 sampai 3 foto dokumentasi kerusakan jalan</p>
                    </div>
                </div>

                <!-- 40. PREVIEW FOTO KOTAK 1, 2, 3 -->
                <div>
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Preview Foto Sebelum Dikirim:</p>
                    <div class="grid grid-cols-3 gap-4" id="preview-container">
                        <!-- Box 1 -->
                        <div id="box-1" class="h-36 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center text-center p-2 relative overflow-hidden">
                            <span class="text-xs font-bold text-slate-400 uppercase">FOTO 1</span>
                            <span class="text-[10px] text-slate-400">Kosong</span>
                        </div>
                        <!-- Box 2 -->
                        <div id="box-2" class="h-36 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center text-center p-2 relative overflow-hidden">
                            <span class="text-xs font-bold text-slate-400 uppercase">FOTO 2</span>
                            <span class="text-[10px] text-slate-400">Kosong</span>
                        </div>
                        <!-- Box 3 -->
                        <div id="box-3" class="h-36 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center text-center p-2 relative overflow-hidden">
                            <span class="text-xs font-bold text-slate-400 uppercase">FOTO 3</span>
                            <span class="text-[10px] text-slate-400">Kosong</span>
                        </div>
                    </div>
                    <p id="photo-limit-warning" class="text-xs font-bold text-rose-600 mt-2 hidden">
                        ⚠️ Batas maksimal foto telah tercapai (Maksimal 3 foto).
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('masyarakat.dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit" id="btn-submit" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-sm shadow-xl shadow-amber-500/25 hover:scale-105 active:scale-95 transition">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Laporan Pengaduan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet-gesture-handling@1.2.2/dist/leaflet-gesture-handling.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var defaultLat = {{ (float) \App\Models\SystemSetting::get('default_map_lat', -6.9200) }};
        var defaultLng = {{ (float) \App\Models\SystemSetting::get('default_map_lng', 107.6250) }};
        var initLat = parseFloat(document.getElementById('latitude').value) || defaultLat;
        var initLng = parseFloat(document.getElementById('longitude').value) || defaultLng;

        var map = L.map('picker-map', {
            zoomControl: true,
            scrollWheelZoom: true,
            gestureHandling: true
        }).setView([initLat, initLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

        function updateCoords(lat, lng, shouldGeocode = false) {
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);
            if (shouldGeocode) {
                reverseGeocodeAddress(lat, lng);
            }
        }

        // Reverse Geocoding to automatically fill address fields
        function reverseGeocodeAddress(lat, lng) {
            var statusEl = document.getElementById('gps-status-text');
            if (statusEl) {
                statusEl.innerHTML = '<span class="text-amber-600 font-semibold"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Mengambil data alamat lokasi...</span>';
            }

            var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
            fetch(url, { headers: { 'Accept-Language': 'id' } })
                .then(res => res.json())
                .then(data => {
                    if (data && data.address) {
                        var addr = data.address;
                        var road = addr.road || addr.street || addr.residential || addr.path || addr.pedestrian || '';
                        var district = addr.suburb || addr.municipality || addr.city_district || addr.county || addr.district || '';
                        var village = addr.village || addr.neighbourhood || addr.quarter || addr.hamlet || addr.town || '';
                        var fullAddress = data.display_name || '';

                        if (road) {
                            document.getElementById('road_name').value = road;
                        }
                        if (district) {
                            var cleanDistrict = district.replace(/^Kecamatan\s+/i, '');
                            document.getElementById('kecamatan').value = 'Kecamatan ' + cleanDistrict;
                        }
                        if (village) {
                            var cleanVillage = village.replace(/^(Desa|Kelurahan)\s+/i, '');
                            document.getElementById('desa').value = cleanVillage;
                        }
                        if (fullAddress) {
                            document.getElementById('address_detail').value = fullAddress;
                        }

                        if (statusEl) {
                            statusEl.innerHTML = '<span class="text-emerald-600 font-semibold"><i class="fa-solid fa-circle-check mr-1"></i> Alamat berhasil diisi otomatis sesuai titik GPS!</span>';
                        }
                    } else {
                        if (statusEl) {
                            statusEl.innerHTML = '<span class="text-slate-500 font-semibold">Koordinat GPS diperbarui. Silakan lengkapi nama jalan.</span>';
                        }
                    }
                })
                .catch(err => {
                    console.warn('Geocoding error:', err);
                    if (statusEl) {
                        statusEl.innerHTML = '<span class="text-slate-500 font-semibold">Koordinat GPS berhasil diperoleh.</span>';
                    }
                });
        }

        marker.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng, true);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng, true);
        });

        // GPS Geolocation Button with Auto Address Fill
        document.getElementById('btn-detect-gps').addEventListener('click', function() {
            var btn = this;
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Mendeteksi Lokasi...';
            btn.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    map.setView([lat, lng], 17);
                    marker.setLatLng([lat, lng]);
                    updateCoords(lat, lng, true);

                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, function(err) {
                    alert('Gagal mendeteksi GPS secara otomatis. Silakan geser pin pada peta secara manual.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                alert('Browser Anda tidak mendukung fitur Geolocation GPS.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });

        // 16 & 40. PHOTO UPLOAD PREVIEW & 3-PHOTO LIMIT LOGIC
        var dropArea = document.getElementById('drop-area');
        var photoInput = document.getElementById('photo-input');
        var selectedFiles = [];

        dropArea.addEventListener('click', function() {
            photoInput.click();
        });

        photoInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            var warning = document.getElementById('photo-limit-warning');
            
            for (var i = 0; i < files.length; i++) {
                if (selectedFiles.length >= 3) {
                    warning.classList.remove('hidden');
                    break;
                }
                var file = files[i];
                if (file.size > 5 * 1024 * 1024) {
                    alert('File ' + file.name + ' melebihi ukuran maksimal 5 MB!');
                    continue;
                }
                selectedFiles.push(file);
            }

            if (selectedFiles.length >= 3) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }

            updatePreviews();
            syncDataTransfer();
        }

        function updatePreviews() {
            for (var i = 1; i <= 3; i++) {
                var box = document.getElementById('box-' + i);
                var file = selectedFiles[i - 1];

                if (file) {
                    var reader = new FileReader();
                    (function(targetBox, index) {
                        reader.onload = function(e) {
                            targetBox.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover">
                                <button type="button" onclick="removePhoto(${index})" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 text-white text-xs flex items-center justify-center shadow-lg hover:bg-rose-700">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <span class="absolute bottom-1 left-1.5 text-[10px] font-bold text-white bg-navy-950/80 px-1.5 py-0.5 rounded">FOTO ${index + 1}</span>
                            `;
                            targetBox.classList.remove('border-dashed');
                            targetBox.classList.add('border-solid', 'border-amber-500');
                        };
                    })(box, i - 1);
                    reader.readAsDataURL(file);
                } else {
                    box.innerHTML = `
                        <span class="text-xs font-bold text-slate-400 uppercase">FOTO ${i}</span>
                        <span class="text-[10px] text-slate-400">Kosong</span>
                    `;
                    box.classList.remove('border-solid', 'border-amber-500');
                    box.classList.add('border-dashed', 'border-slate-300');
                }
            }
        }

        window.removePhoto = function(index) {
            selectedFiles.splice(index, 1);
            document.getElementById('photo-limit-warning').classList.add('hidden');
            updatePreviews();
            syncDataTransfer();
        };

        function syncDataTransfer() {
            var dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            photoInput.files = dt.files;
        }

        setTimeout(function() {
            map.invalidateSize();
        }, 400);
    });
</script>
@endpush
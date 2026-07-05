@extends('layouts.app')

@section('title', 'Buat Pengaduan')

@section('content')
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Buat Pengaduan Baru</h1>
        <p class="text-gray-500 text-sm mb-6">Isi form berikut dan tandai lokasi kejadian di peta.</p>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Kategori -->
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">
                    <i class="fas fa-tag mr-2 text-blue-500"></i>Informasi Pengaduan
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span
                                class="text-red-500">*</span></label>
                        <select name="category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pengaduan <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            placeholder="Contoh: Jalan berlubang depan SDN 01"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span
                            class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required placeholder="Jelaskan permasalahan secara detail..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Bukti (opsional, bisa lebih dari
                        1)</label>
                    <input type="file" name="photos[]" accept="image/*" multiple
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('photos') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Format JPG/PNG, maks 2MB/foto, maksimal 5 foto — ambil dari
                        beberapa sudut agar bukti lebih jelas.</p>
                    @error('photos.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Geotagging -->
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>Lokasi Kejadian
                </h2>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="location_name" id="location_name" value="{{ old('location_name') }}"
                        required placeholder="Contoh: Jl. Sudirman No.10, Yogyakarta"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location_name') border-red-500 @enderror">
                </div>

                <!-- Tombol deteksi lokasi -->
                <div class="flex gap-2 mb-3">
                    <button type="button" onclick="detectLocation()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-crosshairs"></i> Deteksi Lokasi Saya
                    </button>
                    <span class="text-gray-400 text-sm self-center">atau klik langsung di peta</span>
                </div>

                <p id="locationStatus" class="text-xs text-gray-500 mb-2"></p>

                <!-- Peta Leaflet -->
                <div id="mapPicker" style="height:400px; border-radius:0.5rem; border:2px solid #e5e7eb;"></div>

                <!-- Hidden inputs koordinat -->
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                <div id="koordinatInfo" class="mt-2 text-xs text-gray-500 hidden">
                    <i class="fas fa-check-circle text-green-500"></i>
                    Koordinat: <span id="latDisplay"></span>, <span id="lngDisplay"></span>
                </div>

                @error('latitude')
                    <p class="text-red-500 text-xs mt-1">Lokasi di peta wajib ditandai.</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Pengaduan
                </button>
                <a href="{{ route('dashboard') }}"
                    class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Inisialisasi peta picker — center Yogyakarta
        const map = L.map('mapPicker').setView([-7.7956, 110.3695], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        let marker = null;

        function setMarker(lat, lng) {
            if (marker) map.removeLayer(marker);

            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            marker.bindPopup('Lokasi pengaduan Anda').openPopup();

            // Update input hidden
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);

            // Tampilkan info koordinat
            document.getElementById('latDisplay').textContent = lat.toFixed(6);
            document.getElementById('lngDisplay').textContent = lng.toFixed(6);
            document.getElementById('koordinatInfo').classList.remove('hidden');

            // Marker bisa di-drag untuk penyesuaian
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('latitude').value = pos.lat.toFixed(8);
                document.getElementById('longitude').value = pos.lng.toFixed(8);
                document.getElementById('latDisplay').textContent = pos.lat.toFixed(6);
                document.getElementById('lngDisplay').textContent = pos.lng.toFixed(6);
            });
        }

        // Klik peta = set marker
        map.on('click', function(e) {
            setMarker(e.latlng.lat, e.latlng.lng);
            document.getElementById('locationStatus').textContent = 'Lokasi ditandai dari klik peta.';
        });

        // Deteksi lokasi GPS perangkat
        function detectLocation() {
            const status = document.getElementById('locationStatus');
            status.textContent = 'Mendeteksi lokasi...';

            if (!navigator.geolocation) {
                status.textContent = 'Browser tidak mendukung geolocation.';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 17);
                    setMarker(lat, lng);
                    status.textContent = '✓ Lokasi berhasil dideteksi dari perangkat.';
                },
                function(error) {
                    const msg = {
                        1: 'Izin lokasi ditolak. Silakan klik peta secara manual.',
                        2: 'Lokasi tidak tersedia.',
                        3: 'Waktu habis. Coba lagi.',
                    };
                    status.textContent = msg[error.code] || 'Gagal mendeteksi lokasi.';
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        }

        // Jika ada nilai lama (validasi gagal), restore marker
        @if (old('latitude') && old('longitude'))
            setMarker({{ old('latitude') }}, {{ old('longitude') }});
            map.setView([{{ old('latitude') }}, {{ old('longitude') }}], 15);
        @endif
    </script>
@endpush

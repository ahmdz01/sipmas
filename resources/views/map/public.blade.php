@extends('layouts.app')

@section('title', 'Peta Pengaduan')

@section('content')
<div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Peta Pengaduan Masyarakat</h1>
        <p class="text-gray-500 text-sm">Sebaran laporan pengaduan secara real-time</p>
    </div>
    @guest
        <a href="{{ route('complaints.create') }}" 
           onclick="event.preventDefault(); window.location='{{ route('login') }}'"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 inline-flex items-center gap-2">
            <i class="fas fa-plus"></i> Buat Pengaduan
        </a>
    @else
        <a href="{{ route('complaints.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 inline-flex items-center gap-2">
            <i class="fas fa-plus"></i> Buat Pengaduan
        </a>
    @endguest
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-center">
    <span class="text-sm font-semibold text-gray-600">Filter:</span>

    <select id="filterKategori" class="border border-gray-300 rounded px-3 py-1.5 text-sm">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
    </select>

    <select id="filterStatus" class="border border-gray-300 rounded px-3 py-1.5 text-sm">
        <option value="">Semua Status</option>
        <option value="pending">Menunggu</option>
        <option value="verified">Diverifikasi</option>
        <option value="in_progress">Diproses</option>
        <option value="resolved">Selesai</option>
    </select>

    <button onclick="loadMarkers()" class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-700">
        <i class="fas fa-filter mr-1"></i> Terapkan
    </button>
    <button onclick="resetFilter()" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-sm hover:bg-gray-300">
        Reset
    </button>

    <span class="ml-auto text-sm text-gray-500">
        Total: <span id="totalMarker" class="font-bold text-blue-700">0</span> pengaduan
    </span>
</div>

<!-- Peta -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div id="map" class="map-container"></div>
</div>

<!-- Legenda -->
<div class="mt-4 bg-white rounded-lg shadow p-4">
    <p class="text-sm font-semibold text-gray-600 mb-2">Legenda Status:</p>
    <div class="flex flex-wrap gap-3 text-xs">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Menunggu</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Diverifikasi</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span> Diproses</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Selesai</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Inisialisasi peta, center ke Indonesia
const map = L.map('map').setView([-2.5, 118.0], 5);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

let markerLayer = L.layerGroup().addTo(map);

// Warna berdasarkan status
function getStatusColor(status) {
    const colors = {
        pending:     '#facc15',
        verified:    '#3b82f6',
        in_progress: '#a855f7',
        resolved:    '#22c55e',
        rejected:    '#ef4444',
    };
    return colors[status] || '#6b7280';
}

// Buat ikon lingkaran berwarna
function createIcon(color) {
    return L.divIcon({
        className: '',
        html: `<div style="
            width:16px; height:16px; border-radius:50%;
            background:${color}; border:3px solid white;
            box-shadow:0 1px 4px rgba(0,0,0,0.4);">
        </div>`,
        iconSize: [16, 16],
        iconAnchor: [8, 8],
        popupAnchor: [0, -10],
    });
}

// Load marker dari API GeoJSON
function loadMarkers() {
    const kategori = document.getElementById('filterKategori').value;
    const status   = document.getElementById('filterStatus').value;

    let url = '/api/complaints/geojson?';
    if (kategori) url += `category_id=${kategori}&`;
    if (status)   url += `status=${status}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            markerLayer.clearLayers();
            document.getElementById('totalMarker').textContent = data.features.length;

            data.features.forEach(feature => {
                const [lng, lat] = feature.geometry.coordinates;
                const p = feature.properties;
                const color = getStatusColor(p.status);

                const marker = L.marker([lat, lng], { icon: createIcon(color) });

                marker.bindPopup(`
                    <div style="min-width:200px">
                        <p class="font-bold text-sm">${p.title}</p>
                        <p class="text-xs text-gray-500 mt-1">${p.complaint_number}</p>
                        <hr class="my-2">
                        <p class="text-xs"><b>Kategori:</b> ${p.category}</p>
                        <p class="text-xs"><b>Lokasi:</b> ${p.location_name}</p>
                        <p class="text-xs"><b>Pelapor:</b> ${p.reporter}</p>
                        <p class="text-xs"><b>Tanggal:</b> ${p.created_at}</p>
                        <p class="text-xs mt-1">
                            <span style="background:${color};color:white;padding:2px 6px;border-radius:999px;font-size:10px">
                                ${p.status_label}
                            </span>
                        </p>
                        ${p.url ? `<a href="${p.url}" class="text-blue-600 text-xs mt-2 block hover:underline">Lihat Detail →</a>` : ''}
                    </div>
                `);

                markerLayer.addLayer(marker);
            });
        });
}

function resetFilter() {
    document.getElementById('filterKategori').value = '';
    document.getElementById('filterStatus').value   = '';
    loadMarkers();
}

// Load saat halaman pertama kali dibuka
loadMarkers();
</script>
@endpush
@extends('layouts.admin')

@section('title', 'Peta Sebaran Pengaduan')

@section('content')

<!-- Stats Bar -->
<div class="grid grid-cols-4 gap-4 mb-5">
    @foreach([
        ['label'=>'Total',    'value'=>$stats['total'],       'color'=>'blue'],
        ['label'=>'Menunggu', 'value'=>$stats['pending'],      'color'=>'yellow'],
        ['label'=>'Diproses', 'value'=>$stats['in_progress'],  'color'=>'purple'],
        ['label'=>'Selesai',  'value'=>$stats['resolved'],     'color'=>'green'],
    ] as $s)
    <div class="bg-white rounded-lg shadow p-3 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-{{ $s['color'] }}-100
                    flex items-center justify-center flex-shrink-0">
            <span class="text-{{ $s['color'] }}-700 font-bold text-sm">{{ $s['value'] }}</span>
        </div>
        <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
    </div>
    @endforeach
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-center">
    <span class="text-sm font-semibold text-gray-600">Filter Peta:</span>

    <select id="filterKategori"
            class="border border-gray-300 rounded px-3 py-1.5 text-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
    </select>

    <select id="filterStatus"
            class="border border-gray-300 rounded px-3 py-1.5 text-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Semua Status</option>
        <option value="pending">Menunggu</option>
        <option value="verified">Diverifikasi</option>
        <option value="in_progress">Diproses</option>
        <option value="resolved">Selesai</option>
    </select>

    <button onclick="loadMarkers()"
            class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">
        <i class="fas fa-filter mr-1"></i> Terapkan
    </button>
    <button onclick="resetFilter()"
            class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded text-sm hover:bg-gray-300">
        Reset
    </button>

    <span class="ml-auto text-sm text-gray-500">
        Menampilkan <span id="totalMarker" class="font-bold text-blue-700">0</span> titik
    </span>
</div>

<!-- Peta -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div id="mapAdmin" style="height:580px;"></div>
</div>

<!-- Legenda -->
<div class="mt-4 bg-white rounded-lg shadow p-4 flex flex-wrap gap-6">
    <div>
        <p class="text-xs font-semibold text-gray-600 mb-2">Status:</p>
        <div class="flex flex-wrap gap-3 text-xs">
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Menunggu
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Diverifikasi
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span> Diproses
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Selesai
            </span>
        </div>
    </div>
    <div class="border-l pl-6">
        <p class="text-xs font-semibold text-gray-600 mb-2">Kategori:</p>
        <div class="flex flex-wrap gap-3 text-xs">
            @foreach($categories as $cat)
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full inline-block"
                      style="background:{{ $cat->color }}"></span>
                {{ $cat->name }}
            </span>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const map = L.map('mapAdmin').setView([-2.5, 118.0], 5);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

let markerLayer = L.layerGroup().addTo(map);

function getStatusColor(status) {
    const c = {
        pending:     '#facc15',
        verified:    '#3b82f6',
        in_progress: '#a855f7',
        resolved:    '#22c55e',
        rejected:    '#ef4444',
    };
    return c[status] || '#6b7280';
}

function createIcon(statusColor, categoryColor) {
    return L.divIcon({
        className: '',
        html: `<div style="
            width:18px; height:18px; border-radius:50%;
            background:${statusColor};
            border:3px solid ${categoryColor};
            box-shadow:0 2px 6px rgba(0,0,0,0.3);">
        </div>`,
        iconSize:   [18, 18],
        iconAnchor: [9, 9],
        popupAnchor:[0, -12],
    });
}

function loadMarkers() {
    const kategori = document.getElementById('filterKategori').value;
    const status   = document.getElementById('filterStatus').value;

    let url = '/api/complaints/geojson?';
    if (kategori) url += `category_id=${kategori}&`;
    if (status)   url += `status=${status}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            markerLayer.clearLayers();
            document.getElementById('totalMarker').textContent = data.features.length;

            data.features.forEach(f => {
                const [lng, lat] = f.geometry.coordinates;
                const p = f.properties;
                const statusColor   = getStatusColor(p.status);
                const categoryColor = p.category_color || '#6b7280';

                const m = L.marker([lat, lng], {
                    icon: createIcon(statusColor, categoryColor)
                });

                m.bindPopup(`
                    <div style="min-width:220px; font-family:sans-serif;">
                        <p style="font-weight:bold; font-size:13px; margin:0 0 4px">
                            ${p.title}
                        </p>
                        <p style="font-size:11px; color:#6b7280; margin:0 0 8px">
                            ${p.complaint_number}
                        </p>
                        <hr style="margin:0 0 8px; border-color:#e5e7eb">
                        <p style="font-size:12px; margin:2px 0">
                            <b>Kategori:</b> ${p.category}
                        </p>
                        <p style="font-size:12px; margin:2px 0">
                            <b>Lokasi:</b> ${p.location_name}
                        </p>
                        <p style="font-size:12px; margin:2px 0">
                            <b>Pelapor:</b> ${p.reporter}
                        </p>
                        <p style="font-size:12px; margin:2px 0">
                            <b>Tanggal:</b> ${p.created_at}
                        </p>
                        <div style="margin-top:8px">
                            <span style="background:${statusColor};color:white;
                                padding:2px 8px;border-radius:999px;font-size:10px">
                                ${p.status_label}
                            </span>
                        </div>
                        <a href="${p.url}" style="color:#3b82f6;font-size:12px;
                            margin-top:8px;display:block;text-decoration:none">
                            Lihat & Kelola →
                        </a>
                    </div>
                `);

                markerLayer.addLayer(m);
            });
        });
}

function resetFilter() {
    document.getElementById('filterKategori').value = '';
    document.getElementById('filterStatus').value   = '';
    loadMarkers();
}

loadMarkers();
</script>
@endpush
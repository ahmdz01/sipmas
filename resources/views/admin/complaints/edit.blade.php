@extends('layouts.app')
@section('title', 'Edit Pengaduan')
@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Edit Pengaduan</h1>
    <p class="text-gray-500 text-sm mb-6">Nomor: {{ $complaint->complaint_number }}</p>

    @if($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('complaints.update', $complaint->id) }}"
          method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">Informasi Pengaduan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                    <select name="category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $complaint->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul *</label>
                    <input type="text" name="title"
                           value="{{ old('title', $complaint->title) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                <textarea name="description" rows="4" required
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $complaint->description) }}</textarea>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto (opsional)</label>
                @if($complaint->photo)
                    <img src="{{ asset('storage/'.$complaint->photo) }}"
                         class="w-32 h-24 object-cover rounded mb-2">
                @endif
                <input type="file" name="photo" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">Lokasi Kejadian</h2>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi *</label>
                <input type="text" name="location_name" id="location_name"
                       value="{{ old('location_name', $complaint->location_name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="button" onclick="detectLocation()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 mb-3 flex items-center gap-2">
                <i class="fas fa-crosshairs"></i> Deteksi Ulang Lokasi
            </button>
            <div id="mapEdit" style="height:380px; border-radius:0.5rem; border:2px solid #e5e7eb;"></div>
            <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude', $complaint->latitude) }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $complaint->longitude) }}">
            <p id="koordinatInfo" class="text-xs text-gray-400 mt-2">
                Koordinat saat ini: {{ $complaint->latitude }}, {{ $complaint->longitude }}
            </p>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
            <a href="{{ route('complaints.show', $complaint->id) }}"
               class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-300">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const lat0 = {{ $complaint->latitude }};
const lng0 = {{ $complaint->longitude }};
const map  = L.map('mapEdit').setView([lat0, lng0], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

let marker = L.marker([lat0, lng0], { draggable: true }).addTo(map);

function updateCoords(lat, lng) {
    document.getElementById('latitude').value  = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
    document.getElementById('koordinatInfo').textContent =
        `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
}

marker.on('dragend', e => {
    const p = e.target.getLatLng();
    updateCoords(p.lat, p.lng);
});

map.on('click', e => {
    marker.setLatLng(e.latlng);
    updateCoords(e.latlng.lat, e.latlng.lng);
});

function detectLocation() {
    if (!navigator.geolocation) return alert('Browser tidak mendukung geolocation.');
    navigator.geolocation.getCurrentPosition(pos => {
        const { latitude: lat, longitude: lng } = pos.coords;
        map.setView([lat, lng], 17);
        marker.setLatLng([lat, lng]);
        updateCoords(lat, lng);
    }, () => alert('Gagal mendeteksi lokasi.'));
}
</script>
@endpush
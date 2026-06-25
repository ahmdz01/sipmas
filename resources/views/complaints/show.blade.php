@extends('layouts.app')

@section('title', 'Detail Pengaduan')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">{{ $complaint->complaint_number }}</p>
            <h1 class="text-2xl font-bold text-gray-800">{{ $complaint->title }}</h1>
        </div>
        @php $badge = $complaint->statusBadge(); @endphp
        <span class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $badge['class'] }}">
            {{ $badge['label'] }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Info + Peta -->
        <div class="md:col-span-2 space-y-5">

            <!-- Info Pengaduan -->
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 border-b pb-2 mb-4">Detail Pengaduan</h2>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-400">Kategori</p>
                        <p class="font-medium">{{ $complaint->category->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Pelapor</p>
                        <p class="font-medium">{{ $complaint->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Tanggal Lapor</p>
                        <p class="font-medium">{{ $complaint->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Lokasi</p>
                        <p class="font-medium">{{ $complaint->location_name }}</p>
                    </div>
                    @if($complaint->handler)
                    <div>
                        <p class="text-gray-400">Ditangani Oleh</p>
                        <p class="font-medium">{{ $complaint->handler->name }}</p>
                    </div>
                    @endif
                    @if($complaint->resolved_at)
                    <div>
                        <p class="text-gray-400">Selesai Pada</p>
                        <p class="font-medium">{{ $complaint->resolved_at->format('d M Y') }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-4">
                    <p class="text-gray-400 text-sm">Deskripsi</p>
                    <p class="text-gray-700 mt-1 text-sm leading-relaxed">{{ $complaint->description }}</p>
                </div>

                @if($complaint->rejection_reason)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded p-3">
                        <p class="text-red-700 text-sm"><strong>Alasan Penolakan:</strong> {{ $complaint->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Foto Bukti -->
            @if($complaint->photo)
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 border-b pb-2 mb-3">Foto Bukti</h2>
                <img src="{{ asset('storage/' . $complaint->photo) }}"
                     alt="Foto pengaduan"
                     class="rounded-lg max-h-64 object-cover w-full">
            </div>
            @endif

            <!-- Mini Peta Lokasi -->
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 border-b pb-2 mb-3">
                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>Lokasi Kejadian
                </h2>
                <div id="mapDetail" style="height:300px; border-radius:0.5rem;"></div>
                <p class="text-xs text-gray-400 mt-2">
                    Koordinat: {{ $complaint->latitude }}, {{ $complaint->longitude }}
                </p>
            </div>
        </div>

        <!-- Kolom Kanan: Timeline + Aksi -->
        <div class="space-y-5">

            <!-- Aksi User -->
            @if(auth()->id() === $complaint->user_id && $complaint->status === 'pending')
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold text-gray-700 mb-3">Aksi</h2>
                <a href="{{ route('complaints.edit', $complaint->id) }}"
                   class="w-full block text-center bg-yellow-500 text-white py-2 rounded-lg text-sm hover:bg-yellow-600 mb-2">
                    <i class="fas fa-edit mr-1"></i> Edit Pengaduan
                </a>
                <form action="{{ route('complaints.destroy', $complaint->id) }}" method="POST"
                      onsubmit="return confirm('Hapus pengaduan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full bg-red-100 text-red-700 py-2 rounded-lg text-sm hover:bg-red-200">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
            @endif

            <!-- Timeline Status -->
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold text-gray-700 border-b pb-2 mb-4">Riwayat Status</h2>
                <div class="space-y-4">
                    @foreach($complaint->updates as $update)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-circle-dot text-blue-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">{{ $update->statusBadge()['label'] ?? $update->status }}</p>
                            <p class="text-xs text-gray-500">{{ $update->note }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $update->user->name }} — {{ $update->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="block text-center text-blue-600 text-sm hover:underline">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const map = L.map('mapDetail').setView([{{ $complaint->latitude }}, {{ $complaint->longitude }}], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

L.marker([{{ $complaint->latitude }}, {{ $complaint->longitude }}])
    .addTo(map)
    .bindPopup('<b>{{ addslashes($complaint->title) }}</b><br>{{ addslashes($complaint->location_name) }}')
    .openPopup();
</script>
@endpush
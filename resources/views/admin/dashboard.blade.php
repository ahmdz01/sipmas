@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

<!-- Statistik Utama -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label'=>'Total Pengaduan', 'value'=>$stats['total'],       'color'=>'blue',   'icon'=>'clipboard-list'],
        ['label'=>'Menunggu',        'value'=>$stats['pending'],      'color'=>'yellow', 'icon'=>'clock'],
        ['label'=>'Diproses',        'value'=>$stats['in_progress'],  'color'=>'purple', 'icon'=>'cog'],
        ['label'=>'Selesai',         'value'=>$stats['resolved'],     'color'=>'green',  'icon'=>'check-circle'],
    ] as $s)
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-{{ $s['color'] }}-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-{{ $s['icon'] }} text-{{ $s['color'] }}-600 text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $s['value'] }}</p>
            <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- Statistik Tambahan -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
            <i class="fas fa-check text-blue-500"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-800">{{ $stats['verified'] }}</p>
            <p class="text-xs text-gray-500">Diverifikasi</p>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
            <i class="fas fa-times text-red-500"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-800">{{ $stats['rejected'] }}</p>
            <p class="text-xs text-gray-500">Ditolak</p>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
            <i class="fas fa-users text-green-500"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-800">{{ $stats['users'] }}</p>
            <p class="text-xs text-gray-500">Total Pelapor</p>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center">
            <i class="fas fa-star text-yellow-400"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-800">
                {{ $stats['avgRating'] > 0 ? $stats['avgRating'] : '-' }}
                @if($stats['avgRating'] > 0)
                    <span class="text-sm font-normal text-gray-400">/5</span>
                @endif
            </p>
            <p class="text-xs text-gray-500">Rating Rata-rata ({{ $stats['totalRatings'] }} ulasan)</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Pengaduan Terbaru -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">Pengaduan Terbaru</h2>
            <a href="{{ route('admin.complaints.index') }}"
               class="text-blue-600 text-xs hover:underline">Lihat Semua →</a>
        </div>
        <div class="divide-y">
            @forelse($latestComplaints as $c)
            @php $badge = $c->statusBadge(); @endphp
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                <div class="flex-1 min-w-0 mr-3">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $c->title }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $c->complaint_number }} · {{ $c->category->name }} · {{ $c->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">
                        {{ $badge['label'] }}
                    </span>
                    <a href="{{ route('admin.complaints.show', $c->id) }}"
                       class="text-blue-600 hover:text-blue-800 text-xs">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">
                Belum ada pengaduan masuk.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pengaduan per Kategori -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-700">Pengaduan per Kategori</h2>
        </div>
        <div class="p-5 space-y-4">
            @foreach($byCategory as $cat)
            @php
                $pct = $stats['total'] > 0
                    ? round(($cat->complaints_count / $stats['total']) * 100)
                    : 0;
            @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700">{{ $cat->name }}</span>
                    <span class="text-gray-500">{{ $cat->complaints_count }} ({{ $pct }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500"
                         style="width: {{ $pct }}%; background-color: {{ $cat->color ?? '#3b82f6' }}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Quick Link ke Peta -->
        <div class="px-5 pb-5">
            <a href="{{ route('admin.map') }}"
               class="w-full block text-center bg-blue-50 text-blue-700 py-2 rounded-lg text-sm
                      font-semibold hover:bg-blue-100 border border-blue-200">
                <i class="fas fa-map mr-1"></i> Lihat Peta Sebaran
            </a>
        </div>
    </div>
</div>

<!-- Grafik Tren Pengaduan Bulanan -->
<div class="bg-white rounded-lg shadow mt-6">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">Tren Pengaduan {{ date('Y') }}</h2>
    </div>
    <div class="p-5">
        <canvas id="monthlyChart" height="90"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const monthlyLabels = {!! json_encode($byMonth->pluck('month')) !!};
    const monthlyData   = {!! json_encode($byMonth->pluck('total')) !!};

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Jumlah Pengaduan',
                data: monthlyData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush

@endsection
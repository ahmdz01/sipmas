<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPMAS — Sistem Pengaduan Masyarakat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        html { scroll-behavior: smooth; }

        .hero-bg {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 50%, #1e3a8a 100%);
        }

        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        }

        .stat-counter {
            font-variant-numeric: tabular-nums;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }
        .delay-3 { animation-delay: 0.3s; opacity: 0; }
        .delay-4 { animation-delay: 0.4s; opacity: 0; }
    </style>
</head>
<body class="bg-gray-50 font-sans flex flex-col min-h-screen">

<!-- ==================== NAVBAR ==================== -->
<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

        <!-- Logo -->
        <a href="{{ route('landing') }}" class="flex items-center gap-2.5 font-bold text-blue-700 text-lg">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-map-marked-alt text-white text-sm"></i>
            </div>
            SIPMAS
        </a>

        <!-- Nav Links -->
        <div class="hidden md:flex items-center gap-8 text-sm text-gray-600">
            <a href="#fitur"     class="hover:text-blue-600 transition">Fitur</a>
            <a href="#cara-kerja" class="hover:text-blue-600 transition">Cara Kerja</a>
            <a href="#kategori"  class="hover:text-blue-600 transition">Kategori</a>
            <a href="{{ route('map.public') }}" class="hover:text-blue-600 transition">Peta</a>
        </div>

        <!-- CTA -->
        <div class="flex items-center gap-3">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="text-sm text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-tachometer-alt mr-1"></i>Admin
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                       class="text-sm text-gray-600 hover:text-blue-600 transition">
                        Dashboard
                    </a>
                    <a href="{{ route('complaints.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-1"></i>Buat Pengaduan
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="text-sm text-gray-600 hover:text-blue-600 transition font-medium">
                    Masuk
                </a>
                <a href="{{ route('register') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                    Daftar Gratis
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- ==================== HERO ==================== -->
<section class="hero-bg text-white py-24 px-6 relative overflow-hidden">

    <!-- Dekorasi lingkaran -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full
                translate-x-32 -translate-y-32 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full
                -translate-x-20 translate-y-20 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Teks -->
            <div>
                <span class="inline-block bg-blue-500 bg-opacity-40 text-blue-100 text-xs font-semibold
                             px-3 py-1 rounded-full mb-4 border border-blue-400 border-opacity-40">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Berbasis Lokasi (Geotagging)
                </span>

                <h1 class="text-4xl md:text-5xl font-black leading-tight mb-5">
                    Laporkan Masalah<br>
                    <span class="text-blue-300">di Sekitar Anda</span><br>
                    dengan Mudah
                </h1>

                <p class="text-blue-100 text-lg leading-relaxed mb-8 max-w-lg">
                    SIPMAS memudahkan masyarakat menyampaikan pengaduan secara online
                    lengkap dengan penandaan lokasi di peta digital, dan memantau
                    progres penanganannya secara transparan.
                </p>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}"
                       class="bg-white text-blue-700 px-6 py-3 rounded-xl font-bold
                              hover:bg-blue-50 transition shadow-lg text-sm">
                        <i class="fas fa-user-plus mr-2"></i>Mulai Melapor
                    </a>
                    <a href="{{ route('map.public') }}"
                       class="bg-blue-500 bg-opacity-40 text-white px-6 py-3 rounded-xl font-bold
                              hover:bg-opacity-60 transition border border-blue-400
                              border-opacity-40 text-sm">
                        <i class="fas fa-map mr-2"></i>Lihat Peta
                    </a>
                </div>
            </div>

            <!-- Statistik Card -->
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['icon'=>'clipboard-list', 'value'=>$stats['total'],    'label'=>'Total Pengaduan',  'color'=>'blue'],
                    ['icon'=>'check-circle',   'value'=>$stats['resolved'], 'label'=>'Berhasil Diselesaikan', 'color'=>'green'],
                    ['icon'=>'clock',          'value'=>$stats['pending'],  'label'=>'Menunggu Tindakan', 'color'=>'yellow'],
                    ['icon'=>'users',          'value'=>$stats['users'],    'label'=>'Masyarakat Pelapor','color'=>'purple'],
                ] as $i => $s)
                <div class="bg-white bg-opacity-10 backdrop-blur rounded-2xl p-5 border
                            border-white border-opacity-20 fade-up delay-{{ $i + 1 }}">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-20
                                flex items-center justify-center mb-3">
                        <i class="fas fa-{{ $s['icon'] }} text-white"></i>
                    </div>
                    <p class="text-3xl font-black stat-counter">{{ $s['value'] }}</p>
                    <p class="text-blue-200 text-xs mt-1">{{ $s['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- ==================== CARA KERJA ==================== -->
<section id="cara-kerja" class="py-20 px-6 bg-white">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-14">
            <span class="text-blue-600 text-sm font-semibold uppercase tracking-wider">Mudah & Cepat</span>
            <h2 class="text-3xl font-black text-gray-800 mt-2">Cara Kerja SIPMAS</h2>
            <p class="text-gray-500 mt-3 max-w-xl mx-auto">
                Hanya 3 langkah untuk menyampaikan pengaduan Anda kepada pihak berwenang.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">

            <!-- Garis penghubung (desktop) -->
            <div class="hidden md:block absolute top-10 left-1/4 right-1/4 h-0.5 bg-blue-100 z-0"></div>

            @foreach([
                [
                    'step'  => '01',
                    'icon'  => 'edit',
                    'title' => 'Isi Formulir Pengaduan',
                    'desc'  => 'Pilih kategori, isi judul dan deskripsi masalah, serta lampirkan foto bukti jika ada.',
                    'color' => 'blue',
                ],
                [
                    'step'  => '02',
                    'icon'  => 'map-marker-alt',
                    'title' => 'Tandai Lokasi di Peta',
                    'desc'  => 'Gunakan GPS otomatis atau klik langsung di peta untuk menandai lokasi kejadian secara akurat.',
                    'color' => 'indigo',
                ],
                [
                    'step'  => '03',
                    'icon'  => 'paper-plane',
                    'title' => 'Pantau Status Pengaduan',
                    'desc'  => 'Kirim laporan dan pantau progres penanganan secara real-time melalui dashboard Anda.',
                    'color' => 'green',
                ],
            ] as $step)
            <div class="relative z-10 text-center card-hover bg-gray-50 rounded-2xl p-8 border border-gray-100">
                <div class="w-16 h-16 bg-{{ $step['color'] }}-100 rounded-2xl flex items-center
                            justify-center mx-auto mb-4 relative">
                    <i class="fas fa-{{ $step['icon'] }} text-{{ $step['color'] }}-600 text-xl"></i>
                    <span class="absolute -top-2 -right-2 w-6 h-6 bg-{{ $step['color'] }}-600
                                 text-white text-xs font-bold rounded-full flex items-center justify-center">
                        {{ $step['step'] }}
                    </span>
                </div>
                <h3 class="text-gray-800 font-bold text-lg mb-2">{{ $step['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ==================== FITUR ==================== -->
<section id="fitur" class="py-20 px-6 bg-gray-50">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-14">
            <span class="text-blue-600 text-sm font-semibold uppercase tracking-wider">Keunggulan</span>
            <h2 class="text-3xl font-black text-gray-800 mt-2">Fitur Unggulan SIPMAS</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['icon'=>'map-marked-alt', 'color'=>'blue',   'title'=>'Geotagging Akurat',
                 'desc'=>'Setiap pengaduan dilengkapi koordinat GPS sehingga lokasi permasalahan dapat diidentifikasi secara presisi.'],
                ['icon'=>'chart-bar',      'color'=>'indigo',  'title'=>'Peta Interaktif Real-time',
                 'desc'=>'Visualisasi sebaran seluruh pengaduan pada peta digital OpenStreetMap yang dapat difilter per kategori dan status.'],
                ['icon'=>'shield-alt',     'color'=>'green',   'title'=>'Transparan & Akuntabel',
                 'desc'=>'Pelapor dapat memantau status tindak lanjut pengaduan secara langsung tanpa perlu menghubungi instansi.'],
                ['icon'=>'mobile-alt',     'color'=>'purple',  'title'=>'Responsif di Semua Perangkat',
                 'desc'=>'Dapat diakses dari smartphone, tablet, maupun komputer kapan saja dan di mana saja.'],
                ['icon'=>'history',        'color'=>'orange',  'title'=>'Riwayat Lengkap',
                 'desc'=>'Setiap perubahan status tercatat dalam timeline sehingga proses penanganan dapat ditelusuri.'],
                ['icon'=>'lock',           'color'=>'red',     'title'=>'Aman & Terverifikasi',
                 'desc'=>'Sistem autentikasi memastikan setiap pengaduan berasal dari pengguna terdaftar yang terverifikasi.'],
            ] as $f)
            <div class="bg-white rounded-2xl p-6 border border-gray-100 card-hover">
                <div class="w-12 h-12 bg-{{ $f['color'] }}-100 rounded-xl
                            flex items-center justify-center mb-4">
                    <i class="fas fa-{{ $f['icon'] }} text-{{ $f['color'] }}-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">{{ $f['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ==================== KATEGORI ==================== -->
<section id="kategori" class="py-20 px-6 bg-white">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-14">
            <span class="text-blue-600 text-sm font-semibold uppercase tracking-wider">Jenis Laporan</span>
            <h2 class="text-3xl font-black text-gray-800 mt-2">Kategori Pengaduan</h2>
            <p class="text-gray-500 mt-3">Pilih kategori yang sesuai dengan permasalahan di lingkungan Anda.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
            $catIcons = [
                'Infrastruktur'        => ['icon' => 'road',     'color' => 'red',    'bg' => '#fee2e2'],
                'Kebersihan Lingkungan'=> ['icon' => 'trash',    'color' => 'green',  'bg' => '#dcfce7'],
                'Ketertiban Umum'      => ['icon' => 'shield',   'color' => 'yellow', 'bg' => '#fef9c3'],
                'Fasilitas Publik'     => ['icon' => 'building', 'color' => 'blue',   'bg' => '#dbeafe'],
            ];
            @endphp

            @foreach($categories as $cat)
            @php $ci = $catIcons[$cat->name] ?? ['icon'=>'exclamation','color'=>'gray','bg'=>'#f3f4f6']; @endphp
            <div class="rounded-2xl p-6 text-center card-hover border border-gray-100"
                 style="background: {{ $ci['bg'] }}">
                <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-{{ $ci['icon'] }} text-{{ $ci['color'] }}-500 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-sm mb-1">{{ $cat->name }}</h3>
                <p class="text-2xl font-black text-gray-700">{{ $cat->complaints_count }}</p>
                <p class="text-xs text-gray-500">pengaduan</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ==================== PENGADUAN TERBARU ==================== -->
@if($latestComplaints->count() > 0)
<section class="py-20 px-6 bg-gray-50">
    <div class="max-w-7xl mx-auto">

        <div class="flex items-center justify-between mb-10">
            <div>
                <span class="text-blue-600 text-sm font-semibold uppercase tracking-wider">Terkini</span>
                <h2 class="text-3xl font-black text-gray-800 mt-1">Pengaduan Terbaru</h2>
            </div>
            <a href="{{ route('map.public') }}"
               class="text-blue-600 text-sm font-semibold hover:underline">
                Lihat di Peta →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($latestComplaints as $c)
            @php $badge = $c->statusBadge(); @endphp
            <div class="bg-white rounded-2xl p-5 border border-gray-100 card-hover flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium px-2 py-1 rounded-full {{ $badge['class'] }}">
                        {{ $badge['label'] }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $c->created_at->diffForHumans() }}</span>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm mb-2 flex-1">
                    {{ Str::limit($c->title, 50) }}
                </h3>
                <div class="mt-auto pt-3 border-t border-gray-50 space-y-1">
                    <p class="text-xs text-gray-500 flex items-center gap-1.5">
                        <i class="fas fa-tag text-blue-400"></i>
                        {{ $c->category->name }}
                    </p>
                    <p class="text-xs text-gray-500 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-red-400"></i>
                        {{ Str::limit($c->location_name, 35) }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ==================== CTA BANNER ==================== -->
<section class="py-20 px-6 hero-bg text-white">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-black mb-4">
            Siap Menyampaikan Pengaduan?
        </h2>
        <p class="text-blue-200 text-lg mb-8">
            Bergabunglah bersama masyarakat yang telah menggunakan SIPMAS
            untuk menciptakan lingkungan yang lebih baik.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            @guest
            <a href="{{ route('register') }}"
               class="bg-white text-blue-700 px-8 py-3.5 rounded-xl font-bold
                      hover:bg-blue-50 transition shadow-lg">
                <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang — Gratis
            </a>
            <a href="{{ route('login') }}"
               class="bg-blue-500 bg-opacity-40 text-white px-8 py-3.5 rounded-xl
                      font-bold hover:bg-opacity-60 transition border border-blue-400 border-opacity-40">
                <i class="fas fa-sign-in-alt mr-2"></i>Sudah Punya Akun
            </a>
            @else
            <a href="{{ route('complaints.create') }}"
               class="bg-white text-blue-700 px-8 py-3.5 rounded-xl font-bold
                      hover:bg-blue-50 transition shadow-lg">
                <i class="fas fa-plus mr-2"></i>Buat Pengaduan Sekarang
            </a>
            @endguest
        </div>
    </div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer class="bg-gray-900 text-gray-400 text-sm border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Brand -->
        <div>
            <div class="flex items-center gap-2 text-white font-bold text-base mb-3">
                <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marked-alt text-white text-xs"></i>
                </div>
                SIPMAS
            </div>
            <p class="text-xs leading-relaxed text-gray-500">
                Sistem Pengaduan Masyarakat Terintegrasi Peta Digital.
                Dikembangkan sebagai tugas akhir Program Studi Informatika,
                Universitas Amikom Yogyakarta.
            </p>
        </div>

        <!-- Link -->
        <div>
            <p class="text-white font-semibold text-xs uppercase tracking-wider mb-3">Navigasi</p>
            <div class="space-y-2 text-xs">
                <a href="{{ route('landing') }}"        class="block hover:text-white transition">Beranda</a>
                <a href="{{ route('map.public') }}"     class="block hover:text-white transition">Peta Pengaduan</a>
                <a href="{{ route('complaints.create') }}" class="block hover:text-white transition">Buat Pengaduan</a>
                <a href="{{ route('login') }}"          class="block hover:text-white transition">Masuk</a>
                <a href="{{ route('register') }}"       class="block hover:text-white transition">Daftar</a>
            </div>
        </div>

        <!-- Info -->
        <div>
            <p class="text-white font-semibold text-xs uppercase tracking-wider mb-3">Informasi</p>
            <div class="space-y-2 text-xs text-gray-500">
                <p class="flex items-center gap-2">
                    <i class="fas fa-university text-blue-400 w-4"></i>
                    Universitas Amikom Yogyakarta
                </p>
                <p class="flex items-center gap-2">
                    <i class="fas fa-code-branch text-blue-400 w-4"></i>
                    Laravel 10 + Leaflet.js
                </p>
                <p class="flex items-center gap-2">
                    <i class="fas fa-map text-blue-400 w-4"></i>
                    OpenStreetMap Tile Layer
                </p>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-800 px-6 py-4">
        <p class="text-center text-xs text-gray-600">
            &copy; {{ date('Y') }} SIPMAS — Ahmad Rafdy Ramadhan (23.11.5567).
            All rights reserved.
        </p>
    </div>
</footer>

</body>
</html>
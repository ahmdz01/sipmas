<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPMAS') — Sistem Pengaduan Masyarakat</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Font Awesome (ikon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        .map-container { height: 500px; width: 100%; border-radius: 0.5rem; z-index: 1; }
        .leaflet-popup-content-wrapper { border-radius: 0.5rem; }
        .badge-pending     { background:#fef9c3; color:#854d0e; }
        .badge-verified    { background:#dbeafe; color:#1e40af; }
        .badge-in_progress { background:#f3e8ff; color:#6b21a8; }
        .badge-resolved    { background:#dcfce7; color:#166534; }
        .badge-rejected    { background:#fee2e2; color:#991b1b; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans flex flex-col min-h-screen">

    <!-- NAVBAR -->
    <nav class="bg-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-0 flex items-center justify-between h-14">

            <!-- Logo -->
            <a href="{{ route('map.public') }}" class="flex items-center gap-2 font-bold text-lg">
                <i class="fas fa-map-marked-alt"></i> SIPMAS
            </a>

            <!-- Tengah: Jam Real-Time -->
            <div class="flex flex-col items-center leading-tight">
                <span id="rt-clock" class="font-mono font-bold text-white text-base tabular-nums tracking-widest"></span>
                <span id="rt-date"  class="text-blue-200 text-xs"></span>
            </div>

            <!-- Kanan: Menu -->
            <div class="flex items-center gap-4">
                <a href="{{ route('map.public') }}" class="hover:text-blue-200 text-sm">
                    <i class="fas fa-map mr-1"></i> Peta
                </a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-200 text-sm">
                            <i class="fas fa-tachometer-alt mr-1"></i> Admin
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="hover:text-blue-200 text-sm">
                            <i class="fas fa-list mr-1"></i> Pengaduan Saya
                        </a>
                        {{-- <a href="{{ route('complaints.create') }}"
                           class="bg-white text-blue-700 px-3 py-1.5 rounded-full text-sm font-semibold hover:bg-blue-50">
                            <i class="fas fa-plus mr-1"></i> Buat Pengaduan
                        </a> --}}
                    @endif

                    <div class="relative group">
                        <button class="flex items-center gap-1 text-sm hover:text-blue-200">
                            <i class="fas fa-user-circle text-lg"></i>
                            {{ auth()->user()->name }}
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-40 bg-white text-gray-700 rounded shadow-lg
                                    hidden group-hover:block z-50">
                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-red-600">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hover:text-blue-200 text-sm">Login</a>
                    <a href="{{ route('register') }}"
                       class="bg-white text-blue-700 px-3 py-1.5 rounded-full text-sm font-semibold hover:bg-blue-50">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- FLASH MESSAGE -->
    <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
                <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 font-bold">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded flex items-center justify-between">
                <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 font-bold">&times;</button>
            </div>
        @endif
    </div>

    <!-- KONTEN HALAMAN — flex-1 agar mendorong footer ke bawah -->
    <main class="flex-1 max-w-7xl mx-auto px-4 py-6 w-full">
        @yield('content')
    </main>

    <!-- FOOTER — selalu nempel ke bawah -->
    <footer class="bg-gray-900 text-gray-400 text-sm border-t border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-5 flex flex-col md:flex-row items-center justify-between gap-3">

            <!-- Kiri: Brand -->
            <div class="flex items-center gap-2 text-white font-semibold">
                <i class="fas fa-map-marked-alt text-blue-400"></i>
                <span>SIPMAS</span>
                <span class="text-gray-500 font-normal">— Sistem Pengaduan Masyarakat</span>
            </div>

            <!-- Tengah: Link -->
            <div class="flex items-center gap-5 text-xs">
                <a href="{{ route('map.public') }}" class="hover:text-white transition">
                    <i class="fas fa-map mr-1"></i>Peta
                </a>
                @auth
                <a href="{{ route('dashboard') }}" class="hover:text-white transition">
                    <i class="fas fa-list mr-1"></i>Pengaduan Saya
                </a>
                @endauth
                <a href="{{ route('complaints.create') }}" class="hover:text-white transition">
                    <i class="fas fa-plus mr-1"></i>Buat Pengaduan
                </a>
            </div>

            <!-- Kanan: Copyright -->
            <div class="text-xs text-gray-500">
                &copy; {{ date('Y') }} Universitas Amikom Yogyakarta
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
    const HARI  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const BULAN = ['Januari','Februari','Maret','April','Mei','Juni',
                   'Juli','Agustus','September','Oktober','November','Desember'];

    function updateClockPublic() {
        const now = new Date();
        const hh  = String(now.getHours()).padStart(2, '0');
        const mm  = String(now.getMinutes()).padStart(2, '0');
        const ss  = String(now.getSeconds()).padStart(2, '0');

        document.getElementById('rt-clock').textContent = `${hh}:${mm}:${ss}`;
        document.getElementById('rt-date').textContent  =
            `${HARI[now.getDay()]}, ${now.getDate()} ${BULAN[now.getMonth()]} ${now.getFullYear()}`;
    }

    updateClockPublic();
    setInterval(updateClockPublic, 1000);
    </script>

    @stack('scripts')
</body>
</html>
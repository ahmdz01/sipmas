<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'SIPMAS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .map-container { height: 500px; width: 100%; border-radius: 0.5rem; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">

    <!-- ===================== SIDEBAR ===================== -->
    <aside class="w-60 bg-gray-900 flex flex-col fixed top-0 left-0 h-full z-40">

        <!-- Logo -->
        <div class="px-5 py-5 border-b border-gray-700">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-2 text-white font-bold text-base">
                <i class="fas fa-map-marked-alt text-blue-400 text-lg"></i>
                <span>SIPMAS Admin</span>
            </a>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <p class="text-gray-500 text-xs uppercase font-semibold px-3 mb-2 tracking-wider">
                Menu Utama
            </p>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('admin.dashboard')
                         ? 'bg-blue-600 text-white'
                         : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-tachometer-alt w-4 text-center"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.complaints.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('admin.complaints.*')
                         ? 'bg-blue-600 text-white'
                         : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-clipboard-list w-4 text-center"></i>
                <span>Kelola Pengaduan</span>
                @php $pending = \App\Models\Complaint::where('status','pending')->count(); @endphp
                @if($pending > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold
                                 px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                        {{ $pending }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.map') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('admin.map')
                         ? 'bg-blue-600 text-white'
                         : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-map w-4 text-center"></i>
                <span>Peta Sebaran</span>
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('admin.categories.*')
                         ? 'bg-blue-600 text-white'
                         : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-tags w-4 text-center"></i>
                <span>Kelola Kategori</span>
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('admin.users.*')
                         ? 'bg-blue-600 text-white'
                         : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-users w-4 text-center"></i>
                <span>Kelola User</span>
            </a>

            <div class="border-t border-gray-700 my-3"></div>

            <p class="text-gray-500 text-xs uppercase font-semibold px-3 mb-2 tracking-wider">
                Lainnya
            </p>

            <a href="{{ route('map.public') }}"
               target="_blank"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                      text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                <i class="fas fa-globe w-4 text-center"></i>
                <span>Lihat Situs</span>
                <i class="fas fa-external-link-alt ml-auto text-xs text-gray-500"></i>
            </a>

        </nav>

        <!-- User Info + Logout -->
        <div class="px-4 py-4 border-t border-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div class="overflow-hidden">
                    <p class="text-white text-sm font-semibold truncate">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-gray-400 text-xs truncate">
                        {{ auth()->user()->email }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                               text-red-400 hover:bg-red-900 hover:text-red-300 transition-colors">
                    <i class="fas fa-sign-out-alt w-4 text-center"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ===================== MAIN CONTENT ===================== -->
    <div class="flex-1 flex flex-col ml-60">

        <!-- Top Bar -->
        <header class="bg-white shadow-sm px-6 py-3.5 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-2">
                <h1 class="text-gray-700 font-semibold text-base">
                    @yield('title', 'Dashboard')
                </h1>
            </div>
            <div class="flex items-center gap-4">
                <!-- Notif badge pending -->
                @if($pending > 0)
                <a href="{{ route('admin.complaints.index', ['status' => 'pending']) }}"
                   class="relative text-gray-500 hover:text-blue-600">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs
                                 rounded-full w-4 h-4 flex items-center justify-center font-bold">
                        {{ $pending > 9 ? '9+' : $pending }}
                    </span>
                </a>
                @endif
                <div class="hidden md:flex flex-col items-end">
    <span id="realtime-clock" class="text-gray-700 font-semibold text-sm tabular-nums"></span>
    <span id="realtime-date" class="text-gray-400 text-xs"></span>
</div>
            </div>
        </header>

        <!-- Flash Message -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3
                            rounded-lg text-sm flex items-center justify-between mb-2">
                    <span><i class="fas fa-check-circle mr-2 text-green-500"></i>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()"
                            class="text-green-500 hover:text-green-700 ml-4 font-bold">×</button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3
                            rounded-lg text-sm flex items-center justify-between mb-2">
                    <span><i class="fas fa-exclamation-circle mr-2 text-red-500"></i>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()"
                            class="text-red-500 hover:text-red-700 ml-4 font-bold">×</button>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="px-6 py-3 text-xs text-gray-400 border-t bg-white">
            © {{ date('Y') }} SIPMAS — Universitas Amikom Yogyakarta
        </footer>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const HARI  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const BULAN = ['Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'];

function updateClock() {
    const now  = new Date();
    const hh   = String(now.getHours()).padStart(2, '0');
    const mm   = String(now.getMinutes()).padStart(2, '0');
    const ss   = String(now.getSeconds()).padStart(2, '0');
    const hari = HARI[now.getDay()];
    const tgl  = now.getDate();
    const bln  = BULAN[now.getMonth()];
    const thn  = now.getFullYear();

    document.getElementById('realtime-clock').textContent = `${hh}:${mm}:${ss}`;
    document.getElementById('realtime-date').textContent  = `${hari}, ${tgl} ${bln} ${thn}`;
}

updateClock();
setInterval(updateClock, 1000);
</script>
@stack('scripts')
</body>
</html>
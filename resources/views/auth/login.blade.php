<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIPMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gradient-to-br from-blue-700 to-blue-900 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
            <i class="fas fa-map-marked-alt text-blue-600 text-2xl"></i>
        </div>
        <h1 class="text-white text-2xl font-bold">SIPMAS</h1>
        <p class="text-blue-200 text-sm mt-1">Sistem Pengaduan Masyarakat</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-gray-800 font-bold text-xl mb-1">Masuk ke Akun</h2>
        <p class="text-gray-400 text-sm mb-6">Silakan masukkan email dan password Anda.</p>

        <!-- Session Error -->
        @if(session('status'))
            <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-2 rounded-lg text-sm mb-4">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-envelope text-sm"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="nama@email.com"
                           class="w-full pl-9 pr-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-lock text-sm"></i>
                    </span>
                    <input type="password" name="password" required
                           placeholder="••••••••"
                           class="w-full pl-9 pr-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}">
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
        <input type="checkbox"
               name="remember"
               value="1"
               id="remember"
               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
        <span>Ingat saya selama 30 hari</span>
    </label>
    @if(Route::has('password.request'))
        <a href="{{ route('password.request') }}"
           class="text-sm text-blue-600 hover:underline">Lupa password?</a>
    @endif
</div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold
                           hover:bg-blue-700 transition text-sm flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">
                Daftar sekarang
            </a>
        </p>
    </div>

    <p class="text-center text-blue-200 text-xs mt-6">
        © {{ date('Y') }} SIPMAS — Universitas Amikom Yogyakarta
    </p>
</div>

</body>
</html>
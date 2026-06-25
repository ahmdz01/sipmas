@extends('layouts.app')
@section('title', 'Halaman Tidak Ditemukan')
@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center">
        <div class="text-8xl font-black text-blue-100 mb-4">404</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Halaman Tidak Ditemukan</h1>
        <p class="text-gray-500 mb-6">
            Halaman yang Anda cari tidak ada atau sudah dipindahkan.
        </p>
        <a href="{{ route('map.public') }}"
           class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 text-sm font-semibold">
            <i class="fas fa-home mr-2"></i>Ke Beranda
        </a>
    </div>
</div>
@endsection
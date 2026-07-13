@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Header profil -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-blue-700 text-white flex items-center justify-center text-2xl font-bold flex-shrink-0">
                {{ Str::upper(Str::substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ $user->name }}</h1>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $user->isAdmin() ? 'Admin' : 'Masyarakat' }}
                </span>
            </div>
        </div>

        <!-- Informasi Profil -->
        <div class="bg-white rounded-lg shadow p-5 sm:p-6">
            <h2 class="font-semibold text-gray-700 mb-4 border-b pb-3 flex items-center gap-2 text-base">
                <i class="fas fa-user-circle text-blue-500"></i> Informasi Profil
            </h2>
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Ubah Password -->
        <div class="bg-white rounded-lg shadow p-5 sm:p-6">
            <h2 class="font-semibold text-gray-700 mb-4 border-b pb-2 flex items-center gap-2">
                <i class="fas fa-lock text-yellow-500"></i> Ubah Password
            </h2>
            @include('profile.partials.update-password-form')
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-lg shadow border border-red-200 p-5 sm:p-6">
            <h2 class="font-semibold text-red-600 mb-4 border-b border-red-100 pb-2 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Zona Berbahaya
            </h2>
            @include('profile.partials.delete-user-form')
        </div>

    </div>
@endsection
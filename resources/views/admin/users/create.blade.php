@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')

<div class="max-w-lg bg-white rounded-lg shadow p-6">
    <h2 class="font-semibold text-gray-700 mb-5">Tambah User Baru</h2>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('phone') ? 'border-red-400' : 'border-gray-300' }}">
            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat (opsional)</label>
            <input type="text" name="address" value="{{ old('address') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('address') ? 'border-red-400' : 'border-gray-300' }}">
            @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                           {{ $errors->has('role') ? 'border-red-400' : 'border-gray-300' }}">
                <option value="masyarakat" {{ old('role') === 'masyarakat' ? 'selected' : '' }}>Masyarakat</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" placeholder="Minimal 8 karakter"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                          {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}">
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 text-sm hover:text-gray-700">Batal</a>
        </div>
    </form>
</div>

@endsection
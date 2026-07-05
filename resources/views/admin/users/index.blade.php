@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')

<!-- Filter & Search -->
<div class="bg-white rounded-lg shadow p-4 mb-5">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-end">

        <div>
            <label class="block text-xs text-gray-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nama / email / no. HP..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Role</label>
            <select name="role"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="masyarakat" {{ request('role') === 'masyarakat' ? 'selected' : '' }}>Masyarakat</option>
            </select>
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            <i class="fas fa-search mr-1"></i> Cari
        </button>

        @if(request()->hasAny(['search','role']))
        <a href="{{ route('admin.users.index') }}"
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
            Reset
        </a>
        @endif

        <a href="{{ route('admin.users.create') }}"
           class="ml-auto bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 flex items-center gap-2">
            <i class="fas fa-user-plus"></i> Tambah User
        </a>
    </form>
</div>

<!-- Tabel -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">
            Daftar User
            <span class="text-gray-400 font-normal text-sm ml-1">
                ({{ $users->total() }} user)
            </span>
        </h2>
    </div>

    @if($users->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-users text-5xl mb-3 block"></i>
            <p>Tidak ada data user.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Nama</th>
                        <th class="px-5 py-3 text-left">Email</th>
                        <th class="px-5 py-3 text-left">No. HP</th>
                        <th class="px-5 py-3 text-left">Role</th>
                        <th class="px-5 py-3 text-left">Pengaduan</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="text-xs text-blue-500 font-normal">(Anda)</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $u->phone ?? '-' }}</td>
                        <td class="px-5 py-3">
                            @if($u->role === 'admin')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                    Admin
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    Masyarakat
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $u->complaints_count }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.edit', $u->id) }}"
                                   class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs hover:bg-yellow-200 font-medium">
                                    <i class="fas fa-pen mr-1"></i> Edit
                                </a>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs hover:bg-red-200 font-medium">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection
@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('content')

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h2 class="font-semibold text-gray-700">
            Daftar Kategori
            <span class="text-gray-400 font-normal text-sm ml-1">
                ({{ $categories->count() }} kategori)
            </span>
        </h2>
        <a href="{{ route('admin.categories.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>

    @if($categories->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-tags text-5xl mb-3 block"></i>
            <p>Belum ada kategori.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Icon</th>
                        <th class="px-5 py-3 text-left">Nama Kategori</th>
                        <th class="px-5 py-3 text-left">Warna</th>
                        <th class="px-5 py-3 text-left">Jumlah Pengaduan</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <span class="w-9 h-9 rounded-full flex items-center justify-center"
                                  style="background-color: {{ $cat->color }}20; color: {{ $cat->color }};">
                                <i class="fas fa-{{ $cat->icon }}"></i>
                            </span>
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $cat->name }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2 text-gray-500 text-xs">
                                <span class="w-4 h-4 rounded-full border" style="background-color: {{ $cat->color }};"></span>
                                {{ $cat->color }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $cat->complaints_count }} pengaduan
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.categories.edit', $cat->id) }}"
                                   class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs hover:bg-yellow-200 font-medium">
                                    <i class="fas fa-pen mr-1"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs hover:bg-red-200 font-medium">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
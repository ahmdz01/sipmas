@extends('layouts.admin')

@section('title', 'Kelola Pengaduan')

@section('content')

<!-- Filter & Search -->
<div class="bg-white rounded-lg shadow p-4 mb-5">
    <form method="GET" action="{{ route('admin.complaints.index') }}"
          class="flex flex-wrap gap-3 items-end">

        <div>
            <label class="block text-xs text-gray-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Judul / nomor tiket / lokasi..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                @foreach(['pending'=>'Menunggu','verified'=>'Diverifikasi',
                          'in_progress'=>'Diproses','resolved'=>'Selesai',
                          'rejected'=>'Ditolak'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Kategori</label>
            <select name="category_id"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            <i class="fas fa-search mr-1"></i> Cari
        </button>

        <a href="{{ route('admin.complaints.export.csv', request()->query()) }}"
           class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-1">
            <i class="fas fa-file-csv"></i> Excel/CSV
        </a>

        <a href="{{ route('admin.complaints.export.pdf', request()->query()) }}"
           class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 flex items-center gap-1">
            <i class="fas fa-file-pdf"></i> PDF
        </a>

        @if(request()->hasAny(['search','status','category_id']))
        <a href="{{ route('admin.complaints.index') }}"
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
            Reset
        </a>
        @endif
    </form>
</div>

<!-- Tabel -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h2 class="font-semibold text-gray-700">
            Daftar Pengaduan
            <span class="text-gray-400 font-normal text-sm ml-1">
                ({{ $complaints->total() }} data)
            </span>
        </h2>
    </div>

    @if($complaints->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-inbox text-5xl mb-3 block"></i>
            <p>Tidak ada data pengaduan.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">No. Tiket</th>
                        <th class="px-5 py-3 text-left">Judul</th>
                        <th class="px-5 py-3 text-left">Pelapor</th>
                        <th class="px-5 py-3 text-left">Kategori</th>
                        <th class="px-5 py-3 text-left">Lokasi</th>
                        <th class="px-5 py-3 text-left">Tanggal</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($complaints as $c)
                    @php $badge = $c->statusBadge(); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">
                            {{ $c->complaint_number }}
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ Str::limit($c->title, 35) }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $c->user->name }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $c->category->name }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ Str::limit($c->location_name, 30) }}
                        </td>
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">
                            {{ $c->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.complaints.show', $c->id) }}"
                               class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs
                                      hover:bg-blue-200 font-medium">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t">
            {{ $complaints->links() }}
        </div>
    @endif
</div>

@endsection
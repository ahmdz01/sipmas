@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pengaduan Saya</h1>
        <p class="text-gray-500 text-sm">Halo, {{ auth()->user()->name }}!</p>
    </div>
    <a href="{{ route('complaints.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 flex items-center gap-2">
        <i class="fas fa-plus"></i> Buat Pengaduan
    </a>
</div>

<!-- Statistik -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label'=>'Total', 'value'=>$stats['total'], 'color'=>'blue', 'icon'=>'clipboard-list'],
        ['label'=>'Menunggu', 'value'=>$stats['pending'], 'color'=>'yellow', 'icon'=>'clock'],
        ['label'=>'Diproses', 'value'=>$stats['in_progress'], 'color'=>'purple', 'icon'=>'cog'],
        ['label'=>'Selesai', 'value'=>$stats['resolved'], 'color'=>'green', 'icon'=>'check-circle'],
    ] as $s)
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-{{ $s['color'] }}-100 flex items-center justify-center">
            <i class="fas fa-{{ $s['icon'] }} text-{{ $s['color'] }}-600"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $s['value'] }}</p>
            <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- Tabel Pengaduan -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">Daftar Pengaduan</h2>
    </div>
    @if($complaints->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-inbox text-4xl mb-3 block"></i>
            <p>Belum ada pengaduan.</p>
            <a href="{{ route('complaints.create') }}" class="text-blue-600 text-sm hover:underline mt-2 inline-block">
                Buat pengaduan pertama Anda →
            </a>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="px-5 py-3 text-left">No. Tiket</th>
                    <th class="px-5 py-3 text-left">Judul</th>
                    <th class="px-5 py-3 text-left">Kategori</th>
                    <th class="px-5 py-3 text-left">Tanggal</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($complaints as $c)
                @php $badge = $c->statusBadge(); @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $c->complaint_number }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ Str::limit($c->title, 40) }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $c->category->name }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $c->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('complaints.show', $c->id) }}"
                           class="text-blue-600 hover:underline text-xs">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-3 border-t">
            {{ $complaints->links() }}
        </div>
    @endif
</div>
@endsection
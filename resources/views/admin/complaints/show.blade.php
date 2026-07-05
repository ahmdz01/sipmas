@extends('layouts.admin')

@section('title', 'Detail Pengaduan')

@section('content')

    <div class="mb-4">
        <a href="{{ route('admin.complaints.index') }}" class="text-blue-600 text-sm hover:underline">
            ← Kembali ke Daftar
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-5 mb-5 flex items-start justify-between">
        <div>
            <p class="text-xs text-gray-400 font-mono mb-1">{{ $complaint->complaint_number }}</p>
            <h1 class="text-xl font-bold text-gray-800">{{ $complaint->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-user mr-1"></i> {{ $complaint->user->name }}
                &nbsp;·&nbsp;
                <i class="fas fa-tag mr-1"></i> {{ $complaint->category->name }}
                &nbsp;·&nbsp;
                <i class="fas fa-calendar mr-1"></i> {{ $complaint->created_at->format('d M Y H:i') }}
            </p>
        </div>
        @php $badge = $complaint->statusBadge(); @endphp
        <span class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $badge['class'] }} flex-shrink-0">
            {{ $badge['label'] }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Kolom Kiri -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Detail Lengkap -->
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 border-b pb-2 mb-4">Informasi Pengaduan</h2>

                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-gray-400 text-xs">Lokasi</p>
                        <p class="font-medium">{{ $complaint->location_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs">Koordinat GPS</p>
                        <p class="font-mono text-xs">{{ $complaint->latitude }}, {{ $complaint->longitude }}</p>
                    </div>
                    @if ($complaint->handler)
                        <div>
                            <p class="text-gray-400 text-xs">Ditangani Oleh</p>
                            <p class="font-medium">{{ $complaint->handler->name }}</p>
                        </div>
                    @endif
                    @if ($complaint->verified_at)
                        <div>
                            <p class="text-gray-400 text-xs">Diverifikasi Pada</p>
                            <p class="font-medium">{{ $complaint->verified_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                    @if ($complaint->resolved_at)
                        <div>
                            <p class="text-gray-400 text-xs">Diselesaikan Pada</p>
                            <p class="font-medium">{{ $complaint->resolved_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>

                <div>
                    <p class="text-gray-400 text-xs mb-1">Deskripsi</p>
                    <p class="text-gray-700 text-sm leading-relaxed bg-gray-50 rounded p-3">
                        {{ $complaint->description }}
                    </p>
                </div>

                @if ($complaint->rejection_reason)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded p-3">
                        <p class="text-red-700 text-sm">
                            <i class="fas fa-times-circle mr-1"></i>
                            <strong>Alasan Penolakan:</strong> {{ $complaint->rejection_reason }}
                        </p>
                    </div>
                @endif
            </div>

            @if ($complaint->photos->count())
                <div class="bg-white rounded-lg shadow p-5">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-3">Foto Bukti
                        ({{ $complaint->photos->count() }})</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ($complaint->photos as $photo)
                            <img src="{{ asset('storage/' . $photo->path) }}" alt="Foto bukti"
                                class="rounded-lg h-32 object-cover w-full cursor-pointer"
                                onclick="window.open('{{ asset('storage/' . $photo->path) }}', '_blank')">
                        @endforeach
                    </div>
                </div>
            @elseif ($complaint->photo)
                <div class="bg-white rounded-lg shadow p-5">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-3">Foto Bukti</h2>
                    <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Foto pengaduan"
                        class="rounded-lg max-h-64 object-cover w-full">
                </div>
            @endif

            <!-- Peta Lokasi -->
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 border-b pb-2 mb-3">
                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>Lokasi di Peta
                </h2>
                <div id="mapAdmin" style="height:320px; border-radius:0.5rem;"></div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="space-y-5">

            <!-- Form Update Status -->
            @if (!in_array($complaint->status, ['resolved', 'rejected']))
                <div class="bg-white rounded-lg shadow p-5">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-4">
                        <i class="fas fa-edit text-blue-500 mr-2"></i>Update Status
                    </h2>

                    <form action="{{ route('admin.complaints.updateStatus', $complaint->id) }}" method="POST"
                        id="formUpdateStatus" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                @foreach ([
            'verified' => ['label' => 'Diverifikasi', 'color' => 'blue', 'icon' => 'check'],
            'in_progress' => ['label' => 'Diproses', 'color' => 'purple', 'icon' => 'cog'],
            'resolved' => ['label' => 'Selesai', 'color' => 'green', 'icon' => 'check-circle'],
            'rejected' => ['label' => 'Ditolak', 'color' => 'red', 'icon' => 'times-circle'],
        ] as $val => $opt)
                                    @if ($val !== $complaint->status)
                                        <label
                                            class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer
                                      hover:bg-gray-50 has-[:checked]:border-{{ $opt['color'] }}-500
                                      has-[:checked]:bg-{{ $opt['color'] }}-50">
                                            <input type="radio" name="status" value="{{ $val }}"
                                                class="text-{{ $opt['color'] }}-600"
                                                onchange="toggleRejection(this.value)">
                                            <span class="text-sm font-medium text-gray-700">
                                                <i
                                                    class="fas fa-{{ $opt['icon'] }} text-{{ $opt['color'] }}-500 mr-1"></i>
                                                {{ $opt['label'] }}
                                            </span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alasan Penolakan (muncul saat pilih rejected) -->
                        <div id="rejectionBox" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" rows="3" placeholder="Jelaskan alasan penolakan..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Foto Bukti Penyelesaian (muncul saat pilih resolved) -->
                        <div id="resolutionPhotoBox" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Foto Bukti Penyelesaian
                            </label>
                            <input type="file" name="resolution_photo" accept="image/*"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
             focus:outline-none focus:ring-2 focus:ring-green-500">
                            <p class="text-xs text-gray-400 mt-1">Upload foto hasil penanganan sebagai bukti ke warga.</p>
                            @error('resolution_photo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catatan -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Catatan (opsional)
                            </label>
                            <textarea name="note" rows="3" placeholder="Tambahkan catatan untuk pelapor..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('note') }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold
                               text-sm hover:bg-blue-700 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <i class="fas fa-lock text-gray-400 text-2xl mb-2 block"></i>
                    <p class="text-sm text-gray-500">
                        Pengaduan ini sudah berstatus
                        <strong>{{ $complaint->statusBadge()['label'] }}</strong>
                        dan tidak dapat diubah lagi.
                    </p>
                </div>
            @endif

            <!-- Penilaian Masyarakat -->
            @if ($complaint->status === 'resolved')
                <div class="bg-white rounded-lg shadow p-5">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-4">
                        <i class="fas fa-star text-yellow-400 mr-2"></i>Penilaian Masyarakat
                    </h2>

                    @if ($complaint->rating)
                        <div class="flex items-center gap-1 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <i
                                    class="fas fa-star {{ $i <= $complaint->rating->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                            @endfor
                            <span class="text-sm text-gray-500 ml-2">({{ $complaint->rating->rating }}/5)</span>
                        </div>
                        @if ($complaint->rating->review)
                            <p class="text-sm text-gray-600 italic bg-gray-50 rounded p-3">
                                "{{ $complaint->rating->review }}"
                            </p>
                        @else
                            <p class="text-xs text-gray-400">Pelapor tidak menuliskan ulasan.</p>
                        @endif
                    @else
                        <p class="text-sm text-gray-400">Pelapor belum memberikan penilaian.</p>
                    @endif
                </div>
            @endif

            <!-- Timeline Riwayat -->
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 border-b pb-2 mb-4">Riwayat Perubahan</h2>
                <div class="relative">
                    <!-- Garis vertikal -->
                    <div class="absolute left-3.5 top-0 bottom-0 w-px bg-gray-200"></div>
                    <div class="space-y-5">
                        @foreach ($complaint->updates as $update)
                            @php $ub = $update->statusBadge(); @endphp
                            <div class="flex gap-4 relative">
                                <div
                                    class="w-7 h-7 rounded-full flex items-center justify-center
                                    flex-shrink-0 z-10 {{ $ub['class'] }}">
                                    <i class="fas fa-circle text-xs"></i>
                                </div>
                                <div class="flex-1 pb-1">
                                    <p class="text-sm font-semibold text-gray-800">{{ $ub['label'] }}</p>
                                    @if ($update->note)
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $update->note }}</p>
                                    @endif
                                    @if ($update->photo)
                                        <img src="{{ asset('storage/' . $update->photo) }}"
                                            class="mt-2 rounded-lg max-h-32 object-cover cursor-pointer"
                                            onclick="window.open('{{ asset('storage/' . $update->photo) }}','_blank')">
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $update->user->name }}
                                        · {{ $update->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Komentar & Tanya Jawab -->
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 border-b pb-2 mb-4">Komentar & Tanya Jawab</h2>

                <div class="space-y-3 max-h-80 overflow-y-auto mb-4">
                    @forelse($complaint->comments as $comment)
                        <div class="flex gap-2">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $comment->user->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div
                                    class="rounded-lg px-3 py-2 text-sm
                                    {{ $comment->user->isAdmin() ? 'bg-purple-50' : 'bg-blue-50' }} text-gray-700">
                                    {{ $comment->message }}
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $comment->user->name }} ({{ $comment->user->isAdmin() ? 'Admin' : 'Pelapor' }})
                                    · {{ $comment->created_at->format('d M H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada komentar.</p>
                    @endforelse
                </div>

                <form action="{{ route('admin.complaints.comments.store', $complaint->id) }}" method="POST"
                    class="flex gap-2">
                    @csrf
                    <input type="text" name="message" maxlength="1000" required placeholder="Balas ke pelapor..."
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                @error('message')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Peta lokasi pengaduan
        const map = L.map('mapAdmin').setView([{{ $complaint->latitude }}, {{ $complaint->longitude }}], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([{{ $complaint->latitude }}, {{ $complaint->longitude }}])
            .addTo(map)
            .bindPopup(`
        <b>{{ addslashes($complaint->title) }}</b><br>
        <small>{{ addslashes($complaint->location_name) }}</small>
    `)
            .openPopup();

        // Toggle kotak alasan penolakan
        function toggleRejection(val) {
            document.getElementById('rejectionBox').classList.toggle('hidden', val !== 'rejected');
            document.getElementById('resolutionPhotoBox').classList.toggle('hidden', val !== 'resolved');
        }
    </script>
@endpush

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        .subtitle { font-size: 10px; color: #666; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; }
        .badge { padding: 2px 6px; border-radius: 8px; font-size: 9px; }
        .b-pending     { background:#fef9c3; color:#854d0e; }
        .b-verified    { background:#dbeafe; color:#1e40af; }
        .b-in_progress { background:#f3e8ff; color:#6b21a8; }
        .b-resolved    { background:#dcfce7; color:#166534; }
        .b-rejected    { background:#fee2e2; color:#991b1b; }
        .footer { margin-top: 16px; font-size: 9px; color: #999; text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Pengaduan — SIPMAS</h1>
    <p class="subtitle">
        Dicetak pada {{ now()->format('d M Y H:i') }}
        @if($filters['status']) &middot; Status: {{ ucfirst($filters['status']) }} @endif
        @if($filters['category']) &middot; Kategori: {{ $filters['category'] }} @endif
        @if($filters['search']) &middot; Pencarian: "{{ $filters['search'] }}" @endif
        &middot; Total: {{ $complaints->count() }} pengaduan
    </p>

    <table>
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Pelapor</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Tanggal Lapor</th>
                <th>Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $c)
            @php $badge = $c->statusBadge(); @endphp
            <tr>
                <td>{{ $c->complaint_number }}</td>
                <td>{{ $c->title }}</td>
                <td>{{ $c->category->name }}</td>
                <td>{{ $c->user->name }}</td>
                <td>{{ $c->location_name }}</td>
                <td><span class="badge b-{{ $c->status }}">{{ $badge['label'] }}</span></td>
                <td>{{ $c->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ $c->resolved_at ? $c->resolved_at->format('d-m-Y H:i') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#999;">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">SIPMAS — Sistem Pengaduan Masyarakat, Universitas Amikom Yogyakarta</div>
</body>
</html>
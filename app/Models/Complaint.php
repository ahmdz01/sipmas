<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'complaint_number', 'user_id', 'category_id', 'title',
        'description', 'location_name', 'latitude', 'longitude',
        'photo', 'status', 'rejection_reason', 'handled_by',
        'verified_at', 'resolved_at',
    ];

    protected $casts = [
        'verified_at'  => 'datetime',
        'resolved_at'  => 'datetime',
        'latitude'     => 'float',
        'longitude'    => 'float',
    ];

    // Relasi ke user pelapor
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke admin penanganan
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // Relasi ke riwayat update
    public function updates()
    {
        return $this->hasMany(ComplaintUpdate::class)->latest();
    }

    // Helper: badge status dengan warna
    public function statusBadge(): array
    {
        return match($this->status) {
            'pending'     => ['label' => 'Menunggu',      'class' => 'bg-yellow-100 text-yellow-800'],
            'verified'    => ['label' => 'Diverifikasi',  'class' => 'bg-blue-100 text-blue-800'],
            'in_progress' => ['label' => 'Diproses',      'class' => 'bg-purple-100 text-purple-800'],
            'resolved'    => ['label' => 'Selesai',       'class' => 'bg-green-100 text-green-800'],
            'rejected'    => ['label' => 'Ditolak',       'class' => 'bg-red-100 text-red-800'],
            default       => ['label' => 'Unknown',       'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    // Generate nomor pengaduan otomatis
    public static function generateNumber(): string
    {
        $year  = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'SPM-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
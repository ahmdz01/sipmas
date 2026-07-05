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
        'verified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'latitude'    => 'float',
        'longitude'   => 'float',
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

    // Relasi ke rating/ulasan (jika sudah diberi)
    public function rating()
    {
        return $this->hasOne(ComplaintRating::class);
    }

    // Helper: badge status dengan warna
    public function statusBadge(): array
    {
        return match ($this->status) {
            'pending'     => ['label' => 'Menunggu', 'class' => 'bg-yellow-100 text-yellow-800'],
            'verified'    => ['label' => 'Diverifikasi', 'class' => 'bg-blue-100 text-blue-800'],
            'in_progress' => ['label' => 'Diproses', 'class' => 'bg-purple-100 text-purple-800'],
            'resolved'    => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
            'rejected'    => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-800'],
            default       => ['label' => 'Unknown', 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    // Generate nomor pengaduan otomatis
    public static function generateNumber(): string
    {
        $year  = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'SPM-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function comments()
    {
        return $this->hasMany(ComplaintComment::class)->oldest();
    }

    public function photos()
    {
        return $this->hasMany(ComplaintPhoto::class)->oldest();
    }

    // Batas waktu (jam) per status sebelum dianggap terlambat
    public const SLA_HOURS = [
        'pending'     => 48,  // 2 hari belum diverifikasi
        'verified'    => 24,  // 1 hari belum mulai diproses
        'in_progress' => 120, // 5 hari belum selesai
    ];

// Deadline SLA untuk status saat ini (null kalau status tidak punya SLA, misal resolved/rejected)
    public function slaDeadline(): ?\Illuminate\Support\Carbon
    {
        if (! array_key_exists($this->status, self::SLA_HOURS)) {
            return null;
        }

        return $this->created_at->copy()->addHours(self::SLA_HOURS[$this->status]);
    }

// Apakah pengaduan ini sudah melewati SLA?
    public function isOverdue() : bool
    {
        $deadline = $this->slaDeadline();
        return $deadline && now()->greaterThan($deadline);
    }

// Sudah terlambat berapa jam (0 jika belum lewat SLA)
    public function overdueHours(): int
    {
        $deadline = $this->slaDeadline();
        if (! $deadline || ! $this->isOverdue()) {
            return 0;
        }
        return (int) $deadline->diffInHours(now());
    }

// Scope: hanya pengaduan yang sudah melewati SLA
    public function scopeOverdue($query)
    {
        return $query->where(function ($q) {
            foreach (self::SLA_HOURS as $status => $hours) {
                $q->orWhere(function ($q2) use ($status, $hours) {
                    $q2->where('status', $status)
                        ->where('created_at', '<=', now()->subHours($hours));
                });
            }
        });
    }
}

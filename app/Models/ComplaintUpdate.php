<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintUpdate extends Model
{
    protected $fillable = ['complaint_id', 'user_id', 'status', 'note', 'photo'];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusBadge(): array
{
    return match($this->status) {
        'pending'     => ['label' => 'Menunggu',     'class' => 'bg-yellow-100 text-yellow-800'],
        'verified'    => ['label' => 'Diverifikasi', 'class' => 'bg-blue-100 text-blue-800'],
        'in_progress' => ['label' => 'Diproses',     'class' => 'bg-purple-100 text-purple-800'],
        'resolved'    => ['label' => 'Selesai',      'class' => 'bg-green-100 text-green-800'],
        'rejected'    => ['label' => 'Ditolak',      'class' => 'bg-red-100 text-red-800'],
        default       => ['label' => $this->status,  'class' => 'bg-gray-100 text-gray-800'],
    };
}
}
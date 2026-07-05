<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintRating extends Model
{
    protected $fillable = ['complaint_id', 'rating', 'review'];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
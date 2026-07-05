<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintUpdateFactory extends Factory
{
    protected $model = ComplaintUpdate::class;

    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::inRandomOrder()->value('id') ?? 1,
            'user_id'      => User::where('role', 'admin')->inRandomOrder()->value('id') ?? 1,
            'status'       => 'verified',
            'note'         => 'Update status pengaduan.',
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
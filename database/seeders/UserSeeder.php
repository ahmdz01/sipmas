<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun tetap untuk testing
        $tetap = [
            ['name' => 'Administrator',   'email' => 'admin@sipmas.com',   'role' => 'admin',       'phone' => '081200000001', 'address' => 'Kantor Dinas, Soreang'],
            ['name' => 'Petugas Lapangan','email' => 'petugas@sipmas.com', 'role' => 'admin',       'phone' => '081200000002', 'address' => 'Jl. Merdeka No. 1, Soreang'],
            ['name' => 'Budi Santoso',    'email' => 'budi@example.com',   'role' => 'masyarakat',  'phone' => '081234567890', 'address' => 'Jl. Sudirman No. 12, Soreang'],
            ['name' => 'Siti Rahayu',     'email' => 'siti@example.com',   'role' => 'masyarakat',  'phone' => '085678901234', 'address' => 'Jl. Ahmad Yani No. 45, Banjaran'],
        ];

        foreach ($tetap as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }

        // 16 user acak tambahan
        User::factory()->count(16)->create();
    }
}
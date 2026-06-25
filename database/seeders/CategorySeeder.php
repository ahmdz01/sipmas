<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Infrastruktur',        'icon' => 'road',     'color' => '#e74c3c'],
            ['name' => 'Kebersihan Lingkungan', 'icon' => 'trash',    'color' => '#2ecc71'],
            ['name' => 'Ketertiban Umum',       'icon' => 'shield',   'color' => '#f39c12'],
            ['name' => 'Fasilitas Publik',      'icon' => 'building', 'color' => '#3498db'],
        ];

        DB::table('categories')->insert($categories);
    }
}
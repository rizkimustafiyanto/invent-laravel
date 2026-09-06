<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Elektronik',
            'ATK',
            'Pakaian',
            'Makanan',
            'Minuman',
            'Kesehatan',
            'Rumah Tangga',
            'Olahraga',
            'Otomotif',
            'Perawatan',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}

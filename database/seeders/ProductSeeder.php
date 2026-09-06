<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::SUPER_ADMIN)->first();
        $categories = Category::query()->get(['id', 'name']);

        Product::factory()
            ->count(30)
            ->make()
            ->each(function (Product $product) use ($admin, $categories): void {
                $category = $categories->isNotEmpty() ? $categories->random() : null;

                $product->created_by = $admin?->id;
                $product->category_id = $category?->id;
                $product->stock = $this->generateStockByCategory($category?->name);
                $product->save();
            });
    }

    private function generateStockByCategory(?string $categoryName): int
    {
        return match ($categoryName) {
            'Elektronik', 'Otomotif' => rand(5, 40),
            'Pakaian', 'Olahraga', 'Perawatan' => rand(20, 120),
            'ATK', 'Rumah Tangga' => rand(40, 200),
            'Makanan', 'Minuman', 'Kesehatan' => rand(50, 250),
            default => rand(25, 150),
        };
    }
}

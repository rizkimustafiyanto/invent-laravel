<?php

namespace Database\Seeders;

use App\Enums\SaleStatus;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::query()->where('stock', '>', 0)->get();
        if ($products->isEmpty()) {
            return;
        }

        Sale::factory()
            ->count(50)
            ->sequence(
                fn ($sequence) => [
                    'code' => sprintf(
                        'SALE%s%04d',
                        now()->format('Ymd'),
                        $sequence->index + 1
                    ),
                ]
            )
            ->create()
            ->each(function (Sale $sale, int $index) use ($products) {
                /** @var Collection<int, Product> $availableProducts */
                $availableProducts = $products->filter(fn (Product $product) => (float) $product->stock > 0)->values();

                if ($availableProducts->isEmpty()) {
                    return;
                }

                $selectedProducts = $availableProducts->count() <= 5
                    ? $availableProducts
                    : $availableProducts->random(rand(1, 5));

                $totalQty = 0;
                $totalAmount = 0;

                foreach ($selectedProducts as $product) {
                    $maxQty = (int) min(5, (float) $product->stock);
                    if ($maxQty < 1) {
                        continue;
                    }
                    $qty = rand(1, $maxQty);

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $product->price,
                        'total_price' => $qty * $product->price,
                    ]);

                    $product->decrement('stock', $qty);
                    $product->refresh();

                    $totalQty += $qty;
                    $totalAmount += $qty * $product->price;
                }

                $sale->update([
                    'code' => sprintf(
                        'SALE%s%04d',
                        now()->format('Ymd'),
                        $index + 1
                    ),
                    'total_qty' => $totalQty,
                    'total_amount' => $totalAmount,
                    'status' => SaleStatus::UNPAID,
                ]);
            });
    }
}

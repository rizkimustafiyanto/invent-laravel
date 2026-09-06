<?php

namespace Database\Seeders;

use App\Enums\SaleStatus;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $sales = Sale::inRandomOrder()
            ->limit(20)
            ->get();

        foreach ($sales as $index => $sale) {

            Payment::create([
                'code' => sprintf(
                    'PAY%s%04d',
                    now()->format('Ymd'),
                    $index + 1
                ),
                'sale_id' => $sale->id,
                'date' => now(),
                'amount' => $sale->total_amount,
            ]);

            $sale->update([
                'status' => SaleStatus::UNPAID,
            ]);
        }
    }
}
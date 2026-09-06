<?php

namespace Database\Factories;

use App\Enums\SaleStatus;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('SALE######'),
            'date' => fake()->dateTimeBetween('-6 months', 'now'),
            'total_qty' => 0,
            'total_amount' => 0,
            'status' => SaleStatus::UNPAID,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => SaleStatus::PAID,
        ]);
    }

    public function unpaid(): static
    {
        return $this->state(fn () => [
            'status' => SaleStatus::UNPAID,
        ]);
    }
}
<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('PAY#####'),
            'sale_id' => null,
            'date' => fake()->dateTimeBetween('-3 months', 'now'),
            'amount' => 0,
        ];
    }
}
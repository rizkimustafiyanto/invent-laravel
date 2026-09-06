<?php

namespace App\Modules\Payment\DTOs;

use App\Enums\PaymentMethod;
use App\Modules\Shared\DTOs\BaseDTO;

class CreatePaymentDTO extends BaseDTO
{
    public function __construct(
        public string $sale_id,
        public string $date,
        public ?PaymentMethod $payment_method = null,
    ) {
    }
}

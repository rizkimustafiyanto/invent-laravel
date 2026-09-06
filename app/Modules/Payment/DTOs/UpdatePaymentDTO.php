<?php

namespace App\Modules\Payment\DTOs;

use App\Enums\PaymentMethod;
use App\Modules\Shared\DTOs\BaseDTO;

class UpdatePaymentDTO extends BaseDTO
{
    public function __construct(
        public ?string $date = null,
        public ?PaymentMethod $payment_method = null,
    ) {
    }
}
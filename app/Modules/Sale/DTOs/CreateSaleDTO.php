<?php

namespace App\Modules\Sale\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class CreateSaleDTO extends BaseDTO
{
    public function __construct(
        public string $date,
        public array $products,
    ) {
    }
}

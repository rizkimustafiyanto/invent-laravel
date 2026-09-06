<?php

namespace App\Modules\SaleDetail\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class CreateSaleDetailDTO extends BaseDTO
{
    public function __construct(
        public string $sale_id,
        public string $product_id,
        public float|string $qty,
    ) {
    }
}

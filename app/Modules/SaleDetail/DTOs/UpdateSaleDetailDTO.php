<?php

namespace App\Modules\SaleDetail\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class UpdateSaleDetailDTO extends BaseDTO
{
    public function __construct(
        public ?string $sale_id = null,
        public ?string $product_id = null,
        public float|string|null $qty = null,
    ) {
    }
}

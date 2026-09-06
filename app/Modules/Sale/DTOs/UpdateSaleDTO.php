<?php

namespace App\Modules\Sale\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class UpdateSaleDTO extends BaseDTO
{
    public function __construct(
        public ?string $code = null,
    ) {
    }
}

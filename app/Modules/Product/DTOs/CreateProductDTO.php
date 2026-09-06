<?php

namespace App\Modules\Product\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class CreateProductDTO extends BaseDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public float|string $price,
        public int $created_by,
        public float|int|string $stock = 0,
        public ?string $category_id = null,
        public ?string $image_path = null,
    ) {
    }
}

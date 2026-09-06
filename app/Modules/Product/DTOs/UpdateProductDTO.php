<?php

namespace App\Modules\Product\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class UpdateProductDTO extends BaseDTO
{
    public function __construct(
        public ?string $code = null,
        public ?string $name = null,
        public float|string|null $price = null,
        public float|int|string|null $stock = null,
        public ?string $category_id = null,
        public ?string $image_path = null,
    ) {
    }
}

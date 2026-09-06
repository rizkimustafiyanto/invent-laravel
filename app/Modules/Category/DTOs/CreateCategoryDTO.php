<?php

namespace App\Modules\Category\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class CreateCategoryDTO extends BaseDTO
{
    public function __construct(
        public string $name,
    ) {
    }
}

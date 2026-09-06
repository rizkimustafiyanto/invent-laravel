<?php

namespace App\Modules\Auth\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class RegisterDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}

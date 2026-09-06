<?php

namespace App\Modules\Auth\DTOs;

use App\Modules\Shared\DTOs\BaseDTO;

class LoginDTO extends BaseDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}

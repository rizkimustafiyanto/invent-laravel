<?php

namespace App\Modules\User\DTOs;

use App\Enums\UserRole;
use App\Modules\Shared\DTOs\BaseDTO;

class CreateUserDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?UserRole $role = null,
    ) {
    }
}

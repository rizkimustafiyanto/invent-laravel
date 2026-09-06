<?php

namespace App\Modules\User\DTOs;

use App\Enums\UserRole;
use App\Modules\Shared\DTOs\BaseDTO;

class UpdateUserDTO extends BaseDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?UserRole $role = null,
    ) {
    }
}

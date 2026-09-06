<?php

namespace App\Modules\Auth\Resources;

use App\Enums\UserRole;
use App\Modules\Shared\Http\Resources\BaseResource;

class AuthUserResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role instanceof UserRole ? $this->role->value : $this->role,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

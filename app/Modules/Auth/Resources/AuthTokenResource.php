<?php

namespace App\Modules\Auth\Resources;

use App\Modules\Shared\Http\Resources\BaseResource;

class AuthTokenResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'token' => $this->resource['token'] ?? null,
            'token_type' => 'Bearer',
            'user' => isset($this->resource['user']) ? new AuthUserResource($this->resource['user']) : null,
        ];
    }
}

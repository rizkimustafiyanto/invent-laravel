<?php

namespace App\Modules\Category\Resources;

use App\Modules\Shared\Http\Resources\BaseResource;

class CategoryResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

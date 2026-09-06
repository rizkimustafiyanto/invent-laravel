<?php

namespace App\Modules\Product\Resources;

use App\Modules\Shared\Http\Resources\BaseResource;

class ProductResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'created_by' => $this->created_by,
            'created_by_name' => $this->creator?->name,
            'image_path' => $this->image_path,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'price' => $this->price,
            'stock' => $this->stock,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

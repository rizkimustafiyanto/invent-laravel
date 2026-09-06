<?php

namespace App\Modules\SaleDetail\Resources;

use App\Modules\Shared\Http\Resources\BaseResource;

class SaleDetailResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'sale_name' => $this->sale?->code,
            'product_id' => $this->product_id,
            'product_name' => $this->product?->name,
            'qty' => $this->qty,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

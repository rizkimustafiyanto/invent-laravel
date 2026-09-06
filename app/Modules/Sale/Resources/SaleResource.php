<?php

namespace App\Modules\Sale\Resources;

use App\Modules\Shared\Http\Resources\BaseResource;

class SaleResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'sale_name' => $this->code,
            'date' => $this->date,
            'total_qty' => $this->total_qty,
            'total_amount' => $this->total_amount,
            'status' => $this->status?->value ?? $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

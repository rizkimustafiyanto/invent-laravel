<?php

namespace App\Modules\Payment\Resources;

use App\Modules\Shared\Http\Resources\BaseResource;

class PaymentResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'date' => $this->date,
            'sale_id' => $this->sale_id,
            'sale_name' => $this->sale?->code,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

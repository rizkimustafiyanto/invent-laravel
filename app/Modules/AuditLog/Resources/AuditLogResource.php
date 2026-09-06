<?php

namespace App\Modules\AuditLog\Resources;

use App\Models\Product;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use App\Modules\Shared\Http\Resources\BaseResource;

class AuditLogResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'auditable_type' => $this->auditable_type,
            'auditable_id' => $this->auditable_id,
            'auditable_name' => $this->resolveAuditableName(),
            'action' => $this->action,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function resolveAuditableName(): ?string
    {
        $auditable = $this->auditable;

        if ($auditable instanceof User) {
            return $auditable->name;
        }

        if ($auditable instanceof Product) {
            return $auditable->name;
        }

        if ($auditable instanceof Sale) {
            return $auditable->code;
        }

        if ($auditable instanceof SaleDetail) {
            return trim(sprintf(
                '%s - %s',
                $auditable->sale?->code ?? 'Sale',
                $auditable->product?->name ?? 'Product'
            ));
        }

        if ($auditable instanceof Payment) {
            return $auditable->code;
        }

        return null;
    }
}

<?php

namespace App\Models;

use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['code', 'date', 'total_qty', 'total_amount', 'status'])]
class Sale extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'total_amount' => 'decimal:2',
            'status' => SaleStatus::class,
        ];
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
}

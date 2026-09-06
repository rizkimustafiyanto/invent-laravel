<?php

namespace App\Modules\Sale\Repositories;

use App\Models\Sale;
use App\Modules\Sale\Repositories\Contracts\SaleRepositoryInterface;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends BaseRepository<Sale>
 */
class SaleRepository extends BaseRepository implements SaleRepositoryInterface
{
    public function __construct(Sale $model)
    {
        parent::__construct($model);
    }

    /**
     * @return list<string>
     */
    protected function searchableColumns(): array
    {
        return ['code', 'status'];
    }

    /**
     * @return list<string>
     */
    protected function sortableColumns(): array
    {
        return ['code', 'date', 'total_qty', 'total_amount', 'status', 'created_at'];
    }

    protected function defaultSortColumn(): ?string
    {
        return 'date';
    }

    protected function query(array $filters = []): Builder
    {
        return parent::query($filters);
    }

    protected function applyCustomFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $this->applyDateRange($query, 'date', $filters);
    }
}

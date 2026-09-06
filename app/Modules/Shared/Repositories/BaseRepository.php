<?php

namespace App\Modules\Shared\Repositories;

use App\Modules\Shared\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 * @implements RepositoryInterface<TModel>
 */
abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(
        protected Model $model
    ) {
    }

    /**
     * @return Collection<int, TModel>
     */
    public function all(): Collection
    {
        return $this->model->get();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->query($filters);

        return $query->paginate($perPage);
    }

    protected function query(array $filters = []): Builder
    {
        $query = $this->model->newQuery();

        $this->applySearch($query, $filters);
        $this->applyCustomFilters($query, $filters);
        $this->applySorting($query, $filters);

        return $query;
    }

    /**
     * @return list<string>
     */
    protected function searchableColumns(): array
    {
        return [];
    }

    /**
     * @return list<string>
     */
    protected function sortableColumns(): array
    {
        return [];
    }

    protected function defaultSortColumn(): ?string
    {
        return null;
    }

    protected function defaultSortDirection(): string
    {
        return 'desc';
    }

    protected function applyCustomFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }

    protected function applySearch(Builder $query, array $filters): Builder
    {
        $term = trim((string) ($filters['search'] ?? ''));

        if ($term === '') {
            return $query;
        }

        $columns = $this->searchableColumns();

        if ($columns === []) {
            return $query;
        }

        $query->where(function (Builder $subQuery) use ($columns, $term): void {
            foreach ($columns as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $subQuery->{$method}($column, 'like', "%{$term}%");
            }
        });

        return $query;
    }

    protected function applySorting(Builder $query, array $filters): Builder
    {
        $sortBy = $filters['sort_by'] ?? $this->defaultSortColumn();
        $sortDirection = strtolower((string) ($filters['sort_direction'] ?? $this->defaultSortDirection()));

        if (!$sortBy) {
            return $query;
        }

        if (!in_array($sortBy, $this->sortableColumns(), true)) {
            return $query;
        }

        if (!in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = $this->defaultSortDirection();
        }

        return $query->orderBy($sortBy, $sortDirection);
    }

    protected function applyDateRange(Builder $query, string $column, array $filters): Builder
    {
        if (!empty($filters['date_from'])) {
            $query->whereDate($column, '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate($column, '<=', $filters['date_to']);
        }

        return $query;
    }

    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int|string $id, array $data): ?Model
    {
        $item = $this->find($id);

        $item?->update($data);

        return $item;
    }

    public function delete(int|string $id): bool|null
    {
        return $this->find($id)?->delete();
    }
}

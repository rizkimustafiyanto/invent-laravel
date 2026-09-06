<?php

namespace App\Modules\Shared\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /**
     * @return Collection<int, TModel>
     */
    public function all(): Collection;

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function find(int|string $id): ?Model;

    /**
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function create(array $data): Model;

    /**
     * @param array<string, mixed> $data
     * @return TModel|null
     */
    public function update(int|string $id, array $data): ?Model;

    public function delete(int|string $id): bool|null;
}

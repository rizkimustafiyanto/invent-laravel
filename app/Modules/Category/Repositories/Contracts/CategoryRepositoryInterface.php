<?php

namespace App\Modules\Category\Repositories\Contracts;

use App\Models\Category;
use App\Modules\Shared\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends RepositoryInterface<Category>
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function findByName(string $name): ?Model;

    /**
     * @return Collection<int, Category>
     */
    public function optionList(array $filters = []): Collection;
}

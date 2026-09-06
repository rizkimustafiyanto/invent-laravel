<?php

namespace App\Modules\User\Repositories;

use App\Models\User;
use App\Modules\User\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Shared\Repositories\BaseRepository;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): User
    {
        return parent::create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }
}

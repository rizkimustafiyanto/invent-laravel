<?php

namespace App\Modules\User\Repositories\Contracts;

use App\Models\User;
use App\Modules\Shared\Contracts\RepositoryInterface;

/**
 * @extends RepositoryInterface<User>
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
}

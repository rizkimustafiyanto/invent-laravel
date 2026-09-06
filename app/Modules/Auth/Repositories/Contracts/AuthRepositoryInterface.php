<?php

namespace App\Modules\Auth\Repositories\Contracts;

use App\Models\User;
use App\Modules\Shared\Contracts\RepositoryInterface;

/**
 * @extends RepositoryInterface<User>
 */
interface AuthRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function verifyCredentials(string $email, string $password): ?User;
}

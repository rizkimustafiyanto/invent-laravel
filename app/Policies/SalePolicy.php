<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use App\Enums\UserRole;

class SalePolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Sale $sale): bool
    {
        return true;
    }

    public function create(User $actor): bool
    {
        return $this->isSuperAdmin($actor);
    }

    public function update(User $actor, Sale $sale): bool
    {
        return $this->isSuperAdmin($actor);
    }

    public function delete(User $actor, Sale $sale): bool
    {
        return $this->isSuperAdmin($actor);
    }

    private function isSuperAdmin(User $user): bool
    {
        return ($user->role instanceof UserRole ? $user->role->value : $user->role) === UserRole::SUPER_ADMIN->value;
    }
}

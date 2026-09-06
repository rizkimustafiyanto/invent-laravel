<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Product $product): bool
    {
        return true;
    }

    public function create(User $actor): bool
    {
        return $this->isSuperAdmin($actor);
    }

    public function update(User $actor, Product $product): bool
    {
        return $this->isSuperAdmin($actor);
    }

    public function delete(User $actor, Product $product): bool
    {
        return $this->isSuperAdmin($actor);
    }

    private function isSuperAdmin(User $user): bool
    {
        return ($user->role instanceof UserRole ? $user->role->value : $user->role) === UserRole::SUPER_ADMIN->value;
    }
}

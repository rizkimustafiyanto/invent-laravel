<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $this->isSuperAdmin($actor);
    }

    public function view(User $actor, User $target): bool
    {
        return $this->isSuperAdmin($actor) || $actor->id === $target->id;
    }

    public function create(User $actor): bool
    {
        return $this->isSuperAdmin($actor);
    }

    public function update(User $actor, User $target): bool
    {
        return $this->isSuperAdmin($actor) || $actor->id === $target->id;
    }

    public function delete(User $actor, User $target): bool
    {
        return $this->isSuperAdmin($actor);
    }

    private function isSuperAdmin(User $user): bool
    {
        return ($user->role instanceof UserRole ? $user->role->value : $user->role) === UserRole::SUPER_ADMIN->value;
    }
}

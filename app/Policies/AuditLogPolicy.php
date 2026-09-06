<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role instanceof UserRole
            ? $user->role === UserRole::SUPER_ADMIN
            : $user->role === UserRole::SUPER_ADMIN->value;
    }
}

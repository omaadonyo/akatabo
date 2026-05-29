<?php

namespace App\Filament\Policies;

use App\Models\User;

class EmployeeRestriction
{
    public static function canDelete(User $user): bool
    {
        return $user->isAdmin();
    }

    public static function canManageUsers(User $user): bool
    {
        return $user->isAdmin();
    }

    public static function canAccessSettings(User $user): bool
    {
        return $user->isAdmin();
    }
}

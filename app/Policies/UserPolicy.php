<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isManager();
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isManager() && $user->outlet_id === $model->outlet_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isManager();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) return true;
        
        // Manager can update users in their outlet, but cannot change super admins
        return $user->isManager() && $user->outlet_id === $model->outlet_id && !$model->isSuperAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isManager() && $user->outlet_id === $model->outlet_id && !$model->isSuperAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isSupervisor();
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isSupervisor() && $user->outlet_id === $model->outlet_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin(); // Hanya Admin yang bisa buat user baru
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) return true;

        // Supervisor bisa update user di outlet yang sama, tapi tidak bisa ubah Admin
        return $user->isSupervisor()
            && $user->outlet_id === $model->outlet_id
            && ! $model->isSuperAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isSupervisor()
            && $user->outlet_id === $model->outlet_id
            && ! $model->isSuperAdmin();
    }
}

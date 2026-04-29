<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Outlet;

class OutletPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Outlet $outlet): bool
    {
        return $user->isSuperAdmin() || $user->outlet_id === $outlet->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Outlet $outlet): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Outlet $outlet): bool
    {
        return $user->isSuperAdmin();
    }
}

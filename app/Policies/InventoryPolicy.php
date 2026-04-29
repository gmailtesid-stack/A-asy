<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Inventory;

class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Semua user bisa lihat (tergantung filter di controller)
    }

    public function view(User $user, Inventory $inventory): bool
    {
        return $user->isSuperAdmin() || $user->outlet_id === $inventory->outlet_id;
    }

    public function update(User $user, Inventory $inventory): bool
    {
        // Hanya Admin atau Manager yang bisa ubah stok manual
        return $user->isSuperAdmin() || ($user->isManager() && $user->outlet_id === $inventory->outlet_id);
    }
}

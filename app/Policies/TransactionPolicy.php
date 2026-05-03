<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Tentukan apakah user bisa melihat struk transaksi.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // Super Admin bisa melihat semuanya
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Manager dan Kasir hanya bisa melihat transaksi di outlet mereka sendiri
        return $user->outlet_id === $transaction->outlet_id;
    }

    /**
     * Tentukan apakah user bisa membatalkan transaksi (jika ada fitur cancel).
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        // Hanya Super Admin atau Supervisor outlet terkait yang bisa hapus/batal
        return $user->isSuperAdmin() || ($user->isSupervisor() && $user->outlet_id === $transaction->outlet_id);
    }
}

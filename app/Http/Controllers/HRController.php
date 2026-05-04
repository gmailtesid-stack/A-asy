<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HRController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,supervisor');
    }

    public function commissions()
    {
        // Get all employees with their total sales and calculated commission
        $employees = User::whereIn('role', ['operator', 'supervisor'])
            ->select('users.*')
            ->selectSub(function($query) {
                $query->from('transactions')
                    ->whereColumn('transactions.user_id', 'users.id')
                    ->where('status', 'completed')
                    ->selectRaw('COALESCE(SUM(total), 0)');
            }, 'total_revenue')
            ->withCount(['transactions as total_sales_count'])
            ->get()
            ->map(function ($user) {
                $user->commission_amount = ($user->total_revenue * $user->commission_rate) / 100;
                return $user;
            });

        return view('hr.commissions', compact('employees'));
    }
}

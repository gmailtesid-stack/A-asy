<?php
$rolesMap = [
    'super_admin' => 'admin',
    'manager'     => 'supervisor',
    'cashier'     => 'operator'
];

foreach (\App\Models\User::all() as $u) {
    if (isset($rolesMap[$u->role])) {
        $roleModel = \App\Models\Role::where('slug', $rolesMap[$u->role])->first();
        if ($roleModel) {
            $u->roles()->syncWithoutDetaching([
                $roleModel->id => ['model_type' => \App\Models\User::class]
            ]);
            echo "Assigned {$roleModel->slug} to {$u->email}\n";
        }
    }
}


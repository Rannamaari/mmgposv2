<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'pos_user'] as $r) {
            Role::findOrCreate($r);
        }
        if ($u = User::first()) {
            $u->assignRole('admin');
        }
    }
}

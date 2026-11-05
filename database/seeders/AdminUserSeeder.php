<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** Creates the first admin for /admin. Set ADMIN_EMAIL / ADMIN_PASSWORD in .env before seeding production. */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => env('ADMIN_EMAIL', 'admin@ashficollection.com')]);
        if (! $user->exists) {
            $user->name = 'Store Admin';
            $user->password = env('ADMIN_PASSWORD', 'password');
        }
        $user->is_admin = true;
        $user->save();
    }
}

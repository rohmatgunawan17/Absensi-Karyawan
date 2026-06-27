<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@absensi.test');
        $admin = User::firstOrNew(['email' => $email]);

        if (! $admin->exists) {
            $admin->fill([
                'name' => 'Administrator',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin12345!')),
            ]);
        }

        $admin->role = 'admin';
        $admin->save();
    }
}

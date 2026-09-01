<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = [
            'name' => 'Super Administrator',
            'email' => 'superadmin@jalanku.go.id',
            'phone' => '081234567890',
            'password' => Hash::make('qwerty123456'),
            'role_id' => 4, // super_admin
            'opd_id' => null,
            'is_active' => true,
        ];

        User::updateOrCreate(['email' => $superadmin['email']], $superadmin);
    }
}

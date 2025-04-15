<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'bigture123@example.com',
            'password' => Hash::make('courierplus123123123@@@'), // Use a strong password
            'verified_by_admin' => 'yes',
            'role_as' => '1',
            'email_verified_at' => now(),
        ]);
    }
}

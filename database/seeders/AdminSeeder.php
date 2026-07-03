<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'mobile_number' => '1111111111',
                'pin_hash' => Hash::make('0000'),
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_active' => true,
            ]
        );
    }
}

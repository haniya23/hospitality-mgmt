<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BasicDataSeeder::class,
            SouthIndiaLocationSeeder::class,
            StaffDepartmentSeeder::class, // Seed departments first
            SystemSeeder::class, // Our comprehensive system seeder
            StaffMemberSeeder::class, // Comprehensive staff with all departments
        ]);
    }
}

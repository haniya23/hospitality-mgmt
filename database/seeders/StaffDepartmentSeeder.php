<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaffDepartment;
use Illuminate\Support\Str;

class StaffDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates default departments for hospitality staff management.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Front Office',
                'code' => 'FRONT_OFFICE',
                'description' => 'Guest reception, check-in/check-out, reservations, and guest inquiries',
                'icon' => 'fas fa-concierge-bell',
                'color' => '#3B82F6', // Blue
            ],
            [
                'name' => 'Housekeeping',
                'code' => 'HOUSEKEEPING',
                'description' => 'Room cleaning, laundry, linen management, and property maintenance',
                'icon' => 'fas fa-broom',
                'color' => '#10B981', // Green
            ],
            [
                'name' => 'Maintenance',
                'code' => 'MAINTENANCE',
                'description' => 'Electrical, plumbing, HVAC, and general repairs',
                'icon' => 'fas fa-tools',
                'color' => '#F59E0B', // Amber
            ],
            [
                'name' => 'Food & Beverage',
                'code' => 'F_AND_B',
                'description' => 'Kitchen operations, dining service, and beverage management',
                'icon' => 'fas fa-utensils',
                'color' => '#EF4444', // Red
            ],
            [
                'name' => 'Security',
                'code' => 'SECURITY',
                'description' => 'Property security, surveillance, and emergency response',
                'icon' => 'fas fa-shield-alt',
                'color' => '#6366F1', // Indigo
            ],
            [
                'name' => 'Guest Services',
                'code' => 'GUEST_SERVICES',
                'description' => 'Tours, transportation, and special guest requests',
                'icon' => 'fas fa-hands-helping',
                'color' => '#8B5CF6', // Purple
            ],
            [
                'name' => 'Administration',
                'code' => 'ADMINISTRATION',
                'description' => 'Accounting, inventory, HR, and back-office operations',
                'icon' => 'fas fa-briefcase',
                'color' => '#6B7280', // Gray
            ],
        ];

        foreach ($departments as $department) {
            StaffDepartment::create([
                'uuid' => Str::uuid(),
                'name' => $department['name'],
                'code' => $department['code'],
                'description' => $department['description'],
                'icon' => $department['icon'],
                'color' => $department['color'],
                'is_active' => true,
            ]);
        }

        $this->command->info('✓ Created ' . count($departments) . ' default staff departments');
    }
}

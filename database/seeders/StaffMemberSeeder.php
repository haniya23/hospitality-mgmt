<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\StaffMember;
use App\Models\StaffDepartment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating staff members...');
        
        // Get the demo owner
        $owner = User::where('email', 'demo@example.com')->first();
        
        if (!$owner) {
            $this->command->error('Demo owner not found! Run BasicDataSeeder first.');
            return;
        }
        
        // Get owner's properties
        $properties = $owner->properties;
        
        if ($properties->isEmpty()) {
            $this->command->error('No properties found for owner! Create properties first.');
            return;
        }
        
        // Get all departments
        $departments = StaffDepartment::all();
        
        if ($departments->isEmpty()) {
            $this->command->error('No departments found! Run StaffDepartmentSeeder first.');
            return;
        }
        
        // Staff data for each department across properties
        $staffData = [
            // Front Office Department
            [
                'department' => 'Front Office',
                'staff' => [
                    [
                        'name' => 'Sarah Johnson',
                        'email' => 'sarah.johnson@staff.com',
                        'mobile' => '9001234501',
                        'role' => 'manager',
                        'job_title' => 'Front Office Manager',
                        'property_index' => 0, // Ocean View Resort
                    ],
                    [
                        'name' => 'Michael Brown',
                        'email' => 'michael.brown@staff.com',
                        'mobile' => '9001234502',
                        'role' => 'supervisor',
                        'job_title' => 'Reception Supervisor',
                        'property_index' => 0,
                        'reports_to_index' => 0, // Reports to Sarah
                    ],
                    [
                        'name' => 'Emma Wilson',
                        'email' => 'emma.wilson@staff.com',
                        'mobile' => '9001234503',
                        'role' => 'staff',
                        'job_title' => 'Receptionist',
                        'property_index' => 1, // City Center Hotel
                        'reports_to_index' => 1, // Reports to Michael
                    ],
                ],
            ],
            
            // Housekeeping Department
            [
                'department' => 'Housekeeping',
                'staff' => [
                    [
                        'name' => 'Priya Sharma',
                        'email' => 'priya.sharma@staff.com',
                        'mobile' => '9001234504',
                        'role' => 'manager',
                        'job_title' => 'Housekeeping Manager',
                        'property_index' => 0,
                    ],
                    [
                        'name' => 'Raj Kumar',
                        'email' => 'raj.kumar@staff.com',
                        'mobile' => '9001234505',
                        'role' => 'supervisor',
                        'job_title' => 'Housekeeping Supervisor',
                        'property_index' => 0,
                        'reports_to_index' => 3,
                    ],
                    [
                        'name' => 'Anita Desai',
                        'email' => 'anita.desai@staff.com',
                        'mobile' => '9001234506',
                        'role' => 'staff',
                        'job_title' => 'Room Attendant',
                        'property_index' => 1,
                        'reports_to_index' => 4,
                    ],
                    [
                        'name' => 'Lakshmi Patel',
                        'email' => 'lakshmi.patel@staff.com',
                        'mobile' => '9001234507',
                        'role' => 'staff',
                        'job_title' => 'Housekeeping Attendant',
                        'property_index' => 2, // Cozy Homestay
                        'reports_to_index' => 4,
                    ],
                ],
            ],
            
            // Maintenance Department
            [
                'department' => 'Maintenance',
                'staff' => [
                    [
                        'name' => 'David Martinez',
                        'email' => 'david.martinez@staff.com',
                        'mobile' => '9001234508',
                        'role' => 'supervisor',
                        'job_title' => 'Maintenance Supervisor',
                        'property_index' => 1,
                    ],
                    [
                        'name' => 'Ahmed Khan',
                        'email' => 'ahmed.khan@staff.com',
                        'mobile' => '9001234509',
                        'role' => 'staff',
                        'job_title' => 'Maintenance Technician',
                        'property_index' => 1,
                        'reports_to_index' => 7,
                    ],
                    [
                        'name' => 'Carlos Silva',
                        'email' => 'carlos.silva@staff.com',
                        'mobile' => '9001234510',
                        'role' => 'staff',
                        'job_title' => 'Electrician',
                        'property_index' => 2,
                        'reports_to_index' => 7,
                    ],
                ],
            ],
            
            // F&B Department
            [
                'department' => 'Food & Beverage',
                'staff' => [
                    [
                        'name' => 'Chef Ramesh',
                        'email' => 'ramesh.chef@staff.com',
                        'mobile' => '9001234511',
                        'role' => 'manager',
                        'job_title' => 'F&B Manager',
                        'property_index' => 0,
                    ],
                    [
                        'name' => 'Maria Garcia',
                        'email' => 'maria.garcia@staff.com',
                        'mobile' => '9001234512',
                        'role' => 'staff',
                        'job_title' => 'Waitress',
                        'property_index' => 0,
                        'reports_to_index' => 10,
                    ],
                ],
            ],
            
            // Security Department
            [
                'department' => 'Security',
                'staff' => [
                    [
                        'name' => 'Vikram Singh',
                        'email' => 'vikram.singh@staff.com',
                        'mobile' => '9001234513',
                        'role' => 'supervisor',
                        'job_title' => 'Security Head',
                        'property_index' => 0,
                    ],
                    [
                        'name' => 'Raju Yadav',
                        'email' => 'raju.yadav@staff.com',
                        'mobile' => '9001234514',
                        'role' => 'staff',
                        'job_title' => 'Security Guard',
                        'property_index' => 1,
                        'reports_to_index' => 12,
                    ],
                ],
            ],
            
            // Guest Services Department
            [
                'department' => 'Guest Services',
                'staff' => [
                    [
                        'name' => 'Lisa Chen',
                        'email' => 'lisa.chen@staff.com',
                        'mobile' => '9001234515',
                        'role' => 'supervisor',
                        'job_title' => 'Guest Relations Supervisor',
                        'property_index' => 0,
                    ],
                    [
                        'name' => 'John D\'Souza',
                        'email' => 'john.dsouza@staff.com',
                        'mobile' => '9001234516',
                        'role' => 'staff',
                        'job_title' => 'Concierge',
                        'property_index' => 2,
                        'reports_to_index' => 14,
                    ],
                ],
            ],
            
            // Administration Department
            [
                'department' => 'Administration',
                'staff' => [
                    [
                        'name' => 'Sneha Reddy',
                        'email' => 'sneha.reddy@staff.com',
                        'mobile' => '9001234517',
                        'role' => 'manager',
                        'job_title' => 'Admin Manager',
                        'property_index' => 0,
                    ],
                    [
                        'name' => 'Arjun Nair',
                        'email' => 'arjun.nair@staff.com',
                        'mobile' => '9001234518',
                        'role' => 'staff',
                        'job_title' => 'Accountant',
                        'property_index' => 0,
                        'reports_to_index' => 16,
                    ],
                ],
            ],
        ];
        
        $createdStaff = [];
        $staffCount = 0;
        
        foreach ($staffData as $deptData) {
            $department = $departments->where('name', $deptData['department'])->first();
            
            if (!$department) {
                $this->command->warn("Department {$deptData['department']} not found, skipping...");
                continue;
            }
            
            foreach ($deptData['staff'] as $staff) {
                // Get property
                $property = $properties[$staff['property_index']] ?? $properties->first();
                
                // Create user account
                $user = User::create([
                    'uuid' => Str::uuid(),
                    'name' => $staff['name'],
                    'email' => $staff['email'],
                    'mobile_number' => $staff['mobile'],
                    'pin_hash' => Hash::make('1234'), // Default PIN: 1234
                    'password' => Hash::make('password'), // Fallback password
                    'is_active' => true,
                ]);
                
                // Create staff member with explicit property_id
                $staffMember = StaffMember::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'property_id' => $property->id, // Ensure property_id is set
                    'department_id' => $department->id,
                    'staff_role' => $staff['role'],
                    'job_title' => $staff['job_title'],
                    'employment_type' => 'full_time',
                    'join_date' => now()->subDays(rand(30, 365)),
                    'phone' => '080' . rand(10000000, 99999999),
                    'emergency_contact' => '911' . rand(1000000, 9999999),
                    'status' => 'active',
                ]);
                
                // Verify property_id was saved
                if (!$staffMember->property_id) {
                    $this->command->error("  ✗ FAILED: {$staff['name']} - property_id not saved!");
                } else {
                    $this->command->info("  ✓ Created: {$staff['name']} ({$staff['role']}) - {$department->name} - Property: {$property->name} (ID: {$property->id})");
                }
                
                $createdStaff[] = $staffMember;
                $staffCount++;
            }
        }
        
        // Now update reports_to relationships
        $this->command->info('Setting up reporting hierarchy...');
        
        $staffIndex = 0;
        foreach ($staffData as $deptData) {
            foreach ($deptData['staff'] as $staff) {
                if (isset($staff['reports_to_index']) && isset($createdStaff[$staff['reports_to_index']])) {
                    $createdStaff[$staffIndex]->update([
                        'reports_to' => $createdStaff[$staff['reports_to_index']]->id,
                    ]);
                    
                    $this->command->info("  ✓ {$createdStaff[$staffIndex]->user->name} reports to {$createdStaff[$staff['reports_to_index']]->user->name}");
                }
                $staffIndex++;
            }
        }
        
        $this->command->info("Staff seeded successfully!");
        $this->command->info("Total staff created: {$staffCount}");
        $this->command->info("Departments covered: 7");
        $this->command->info("Properties covered: {$properties->count()}");
    }
}

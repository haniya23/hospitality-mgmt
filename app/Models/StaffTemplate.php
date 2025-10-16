<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'structure',
        'max_properties',
        'max_accommodations',
        'is_active'
    ];

    protected $casts = [
        'structure' => 'array',
        'is_active' => 'boolean'
    ];

    public static function getTemplates()
    {
        return [
            'single_property' => [
                'name' => 'Single Property Template',
                'type' => 'single_property',
                'description' => 'For properties with 1-5 accommodations',
                'max_properties' => 1,
                'max_accommodations' => 5,
                'structure' => [
                    'owner' => [
                        'role' => 'owner',
                        'permissions' => ['all'],
                        'children' => [
                            'manager' => [
                                'role' => 'manager',
                                'permissions' => ['manage_staff', 'manage_bookings', 'view_reports'],
                                'children' => [
                                    'staff' => [
                                        'role' => 'staff',
                                        'permissions' => ['manage_bookings', 'checkin_checkout']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'multiple_properties' => [
                'name' => 'Multiple Properties Template',
                'type' => 'multiple_properties',
                'description' => 'For owners with multiple properties',
                'max_properties' => null,
                'max_accommodations' => null,
                'structure' => [
                    'owner' => [
                        'role' => 'owner',
                        'permissions' => ['all'],
                        'children' => [
                            'manager' => [
                                'role' => 'manager',
                                'permissions' => ['manage_property', 'manage_staff', 'view_reports'],
                                'children' => [
                                    'supervisor' => [
                                        'role' => 'supervisor',
                                        'permissions' => ['manage_staff', 'manage_bookings'],
                                        'children' => [
                                            'staff' => [
                                                'role' => 'staff',
                                                'permissions' => ['manage_bookings', 'checkin_checkout']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function applyTemplate($templateType, $propertyId, $userId)
    {
        $templates = self::getTemplates();
        
        if (!isset($templates[$templateType])) {
            throw new \Exception('Template not found');
        }

        $template = $templates[$templateType];
        
        // Create staff structure based on template
        self::createStaffFromStructure($template['structure'], $propertyId, $userId);
        
        return true;
    }

    private static function createStaffFromStructure($structure, $propertyId, $userId, $parentId = null)
    {
        foreach ($structure as $key => $config) {
            if ($key === 'owner' && $parentId === null) {
                // Skip owner creation, just process children
                if (isset($config['children'])) {
                    self::createStaffFromStructure($config['children'], $propertyId, $userId, null);
                }
                continue;
            }

            // Create staff member
            $staffMember = StaffMember::create([
                'user_id' => $userId,
                'property_id' => $propertyId,
                'staff_role' => $config['role'],
                'job_title' => ucfirst($config['role']),
                'reports_to' => $parentId,
                'employment_type' => 'full_time',
                'status' => 'active',
                'join_date' => now()
            ]);

            // Create permissions
            if (isset($config['permissions'])) {
                $permissions = [];
                foreach ($config['permissions'] as $permission) {
                    $permissions[$permission] = true;
                }
                
                StaffPermission::create([
                    'staff_member_id' => $staffMember->id,
                    ...$permissions
                ]);
            }

            // Process children
            if (isset($config['children'])) {
                self::createStaffFromStructure($config['children'], $propertyId, $userId, $staffMember->id);
            }
        }
    }
}
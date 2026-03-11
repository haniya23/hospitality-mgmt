<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Operational',
                'slug' => 'operational',
                'description' => 'Daily operational expenses including utilities, supplies, and cleaning materials',
                'color' => '#3B82F6', // Blue
            ],
            [
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Repairs, equipment maintenance, and general upkeep',
                'color' => '#F59E0B', // Amber
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Salaries, wages, benefits, and staff-related expenses',
                'color' => '#10B981', // Green
            ],
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Advertising, promotions, and marketing materials',
                'color' => '#8B5CF6', // Purple
            ],
            [
                'name' => 'Administrative',
                'slug' => 'administrative',
                'description' => 'Legal fees, insurance, licenses, and office expenses',
                'color' => '#6B7280', // Gray
            ],
            [
                'name' => 'B2B Commission',
                'slug' => 'b2b-commission',
                'description' => 'Commission payouts to B2B partners and agents',
                'color' => '#EF4444', // Red
            ],
            [
                'name' => 'Utilities',
                'slug' => 'utilities',
                'description' => 'Electricity, water, gas, internet, and phone bills',
                'color' => '#06B6D4', // Cyan
            ],
            [
                'name' => 'Inventory',
                'slug' => 'inventory',
                'description' => 'Guest supplies, toiletries, linens, and consumables',
                'color' => '#EC4899', // Pink
            ],
        ];

        foreach ($categories as $category) {
            DB::table('expense_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                array_merge($category, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}

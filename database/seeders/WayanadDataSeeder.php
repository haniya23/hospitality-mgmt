<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guest;
use App\Models\B2bPartner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WayanadDataSeeder extends Seeder
{
    public function run(): void
    {
        // Only create 3 B2B partners with reserved customers
        $b2bPartners = [
            [
                'name' => 'Suresh Kumar',
                'partner_name' => 'Suresh Kumar Tours & Travels',
                'email' => 'suresh.kumar@example.com',
                'mobile' => '9876543201',
                'commission_rate' => 12.00,
            ],
            [
                'name' => 'Lakshmi Devi',
                'partner_name' => 'Lakshmi Devi Travel Agency',
                'email' => 'lakshmi.devi@example.com',
                'mobile' => '9876543202',
                'commission_rate' => 15.00,
            ],
            [
                'name' => 'Arun Raj',
                'partner_name' => 'Arun Raj Holiday Planners',
                'email' => 'arun.raj@example.com',
                'mobile' => '9876543203',
                'commission_rate' => 10.00,
            ],
        ];

        // Create 3 users for B2B partners
        foreach ($b2bPartners as $index => $partner) {
            $user = User::create([
                'name' => $partner['name'],
                'mobile_number' => $partner['mobile'],
                'pin_hash' => Hash::make('1234'),
                'email' => $partner['email'],
            ]);

            // Create B2B partner (this will automatically create reserved customer via observer)
            B2bPartner::create([
                'partner_name' => $partner['partner_name'],
                'partner_type' => 'Travel Agency',
                'contact_user_id' => $user->id,
                'email' => $partner['email'],
                'phone' => $partner['mobile'],
                'commission_rate' => $partner['commission_rate'],
                'default_discount_pct' => 5.00,
                'status' => 'active',
            ]);
        }
    }
}
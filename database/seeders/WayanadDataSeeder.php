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
        $wayanadNames = [
            'Ravi Krishnan', 'Priya Nair', 'Suresh Kumar', 'Lakshmi Devi', 'Arun Raj',
            'Meera Menon', 'Vinod Thomas', 'Suja Varghese', 'Rajesh Pillai', 'Kavitha Kumari',
            'Manoj Mohan', 'Deepa Das', 'Santhosh Babu', 'Radha Krishnan', 'Biju George',
            'Suma Nair', 'Anoop Kumar', 'Latha Menon', 'Jayesh Raj', 'Nisha Varma',
            'Prakash Nair', 'Geetha Devi', 'Shibu Thomas', 'Usha Kumari', 'Ramesh Pillai',
            'Sindhu Mohan', 'Vinu Das', 'Sreeja Nair', 'Babu Raj', 'Manju Devi',
            'Saji Kumar', 'Remya Menon', 'Jijo George', 'Smitha Nair', 'Renjith Raj',
            'Bindu Kumari', 'Sabu Thomas', 'Lekha Devi', 'Jayan Pillai', 'Neethu Mohan',
            'Sreekanth Das', 'Parvathy Nair', 'Bineesh Kumar', 'Shobha Menon', 'Rajiv Raj',
            'Sonia Varghese', 'Nithin Thomas', 'Preethi Devi', 'Sujith Pillai', 'Reshma Kumari'
        ];

        $mobileNumbers = [];
        for ($i = 0; $i < 50; $i++) {
            do {
                $mobile = '9' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
            } while (in_array($mobile, $mobileNumbers) || User::where('mobile_number', $mobile)->exists());
            $mobileNumbers[] = $mobile;
        }

        // Create 50 users
        foreach ($wayanadNames as $index => $name) {
            User::create([
                'name' => $name,
                'mobile_number' => $mobileNumbers[$index],
                'pin_hash' => Hash::make('1234'),
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            ]);
        }

        // Create 50 B2B Partners
        foreach ($wayanadNames as $index => $name) {
            $contactUser = User::skip($index)->first();
            B2bPartner::create([
                'partner_name' => $name . ' Tours & Travels',
                'partner_type' => 'Travel Agency',
                'contact_user_id' => $contactUser->id,
                'email' => strtolower(str_replace(' ', '.', $name)) . '.tours@example.com',
                'phone' => $mobileNumbers[$index],
                'commission_rate' => rand(5, 15),
                'default_discount_pct' => rand(3, 8),
                'status' => ['active', 'inactive', 'pending'][rand(0, 2)],
            ]);
        }

        // Create 50 Guests/Customers
        foreach ($wayanadNames as $index => $name) {
            Guest::create([
                'name' => $name,
                'mobile_number' => '8' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'email' => strtolower(str_replace(' ', '.', $name)) . '.guest@example.com',
                'address' => 'Wayanad District, Kerala',
                'id_type' => ['aadhar', 'passport', 'driving_license'][rand(0, 2)],
                'id_number' => str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
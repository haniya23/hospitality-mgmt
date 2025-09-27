<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use App\Models\City;
use App\Models\Pincode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // Check if properties already exist
        if (Property::count() >= 10) {
            $this->command->info('Properties already seeded. Skipping...');
            return;
        }

        // Get existing data
        $categories = PropertyCategory::all();
        $users = User::where('is_admin', false)->get();
        $cities = City::all();
        $pincodes = Pincode::all();

        if ($users->isEmpty()) {
            // Create a demo user if none exists
            $user = User::create([
                'name' => 'Demo Property Owner',
                'email' => 'owner@demo.com',
                'mobile_number' => '9876543210',
                'pin_hash' => bcrypt('1234'),
                'password' => bcrypt('password'),
            ]);
            $users = collect([$user]);
        }

        $propertyNames = [
            'Serene Valley Homestay',
            'Heritage Palace Resort',
            'Mountain View Villa',
            'Coastal Paradise Apartment',
            'Royal Heritage Hotel',
            'Jungle Retreat Resort',
            'City Center Apartment',
            'Beachfront Villa',
            'Hill Station Homestay',
            'Luxury Heritage Suite'
        ];

        $descriptions = [
            'Experience the tranquility of nature in our beautifully designed homestay with modern amenities.',
            'Step into history with our heritage property featuring traditional architecture and royal amenities.',
            'Enjoy breathtaking mountain views from our luxury villa with private gardens and modern facilities.',
            'Modern apartment in the heart of the city with easy access to all major attractions.',
            'Traditional hotel with heritage charm and contemporary comfort for the perfect stay.',
            'Escape to our jungle resort surrounded by lush greenery and wildlife.',
            'Conveniently located apartment with all modern amenities and city views.',
            'Beachfront property with direct access to pristine beaches and ocean views.',
            'Cozy homestay in the hills with panoramic views and warm hospitality.',
            'Luxurious heritage suite with royal treatment and premium amenities.'
        ];

        for ($i = 0; $i < 10; $i++) {
            $category = $categories->random();
            $user = $users->random();
            $city = $cities->random();
            $pincode = $pincodes->where('city_id', $city->id)->random();

            $property = Property::create([
                'owner_id' => $user->id,
                'property_category_id' => $category->id,
                'name' => $propertyNames[$i],
                'description' => $descriptions[$i],
                'status' => ['active', 'pending', 'inactive'][rand(0, 2)],
                'wizard_step_completed' => rand(1, 5),
                'approved_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'approved_by' => rand(0, 1) ? 1 : null, // Admin user ID
                'rejection_reason' => rand(0, 1) ? null : 'Incomplete documentation',
                'uuid' => Str::uuid(),
            ]);

            // Create property location for each property
            $property->location()->create([
                'property_id' => $property->id,
                'country_id' => $city->district->state->country_id,
                'state_id' => $city->district->state_id,
                'district_id' => $city->district_id,
                'city_id' => $city->id,
                'pincode_id' => $pincode->id,
                'address' => 'Sample Address ' . ($i + 1),
                'latitude' => rand(100000, 999999) / 10000, // Random coordinates
                'longitude' => rand(100000, 999999) / 10000,
            ]);
        }
    }
}
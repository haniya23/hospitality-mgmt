<?php

namespace Database\Seeders;

use App\Models\PropertyCategory;
use App\Models\PredefinedAccommodationType;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BasicDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create property categories
        $homestay = PropertyCategory::firstOrCreate(['name' => 'Homestay'], ['description' => 'Rent a house for a day']);
        $heritage = PropertyCategory::firstOrCreate(['name' => 'Heritage'], ['description' => 'Heritage properties with historical significance']);
        $villa = PropertyCategory::firstOrCreate(['name' => 'Villa'], ['description' => 'Private villa accommodation']);
        $apartment = PropertyCategory::firstOrCreate(['name' => 'Apartment'], ['description' => 'Serviced apartments']);
        $hotel = PropertyCategory::firstOrCreate(['name' => 'Hotel'], ['description' => 'Traditional hotel accommodation']);
        $resort = PropertyCategory::firstOrCreate(['name' => 'Resort'], ['description' => 'Resort with recreational facilities']);

        // Create accommodation types for each property category
        $accommodationTypes = [
            $homestay->id => [
                ['name' => 'Entire House', 'description' => 'Complete house for exclusive use'],
                ['name' => 'Private Room', 'description' => 'Private bedroom with shared common areas'],
                ['name' => 'Custom', 'description' => 'Custom accommodation type'],
            ],
            $heritage->id => [
                ['name' => 'Heritage Suite', 'description' => 'Luxurious suite in heritage building'],
                ['name' => 'Palace Room', 'description' => 'Royal themed accommodation'],
                ['name' => 'Courtyard Room', 'description' => 'Room overlooking heritage courtyard'],
                ['name' => 'Custom', 'description' => 'Custom accommodation type'],
            ],
            $villa->id => [
                ['name' => 'Entire Villa', 'description' => 'Complete villa with all amenities'],
                ['name' => 'Villa Suite', 'description' => 'Luxury suite within villa'],
                ['name' => 'Custom', 'description' => 'Custom accommodation type'],
            ],
            $apartment->id => [
                ['name' => '1 BHK', 'description' => 'One bedroom apartment'],
                ['name' => '2 BHK', 'description' => 'Two bedroom apartment'],
                ['name' => '3 BHK', 'description' => 'Three bedroom apartment'],
                ['name' => 'Studio', 'description' => 'Studio apartment'],
                ['name' => 'Custom', 'description' => 'Custom accommodation type'],
            ],
            $hotel->id => [
                ['name' => 'Standard Room', 'description' => 'Basic hotel room'],
                ['name' => 'Deluxe Room', 'description' => 'Enhanced hotel room'],
                ['name' => 'Suite', 'description' => 'Luxury hotel suite'],
                ['name' => 'Executive Room', 'description' => 'Business class room'],
                ['name' => 'Custom', 'description' => 'Custom accommodation type'],
            ],
            $resort->id => [
                ['name' => 'Cottage', 'description' => 'Private cottage accommodation'],
                ['name' => 'Resort Room', 'description' => 'Standard resort room'],
                ['name' => 'Beach Villa', 'description' => 'Beachfront villa'],
                ['name' => 'Tree House', 'description' => 'Unique tree house experience'],
                ['name' => 'Pool Villa', 'description' => 'Villa with private pool'],
                ['name' => 'Custom', 'description' => 'Custom accommodation type'],
            ],
        ];

        foreach ($accommodationTypes as $categoryId => $types) {
            foreach ($types as $type) {
                PredefinedAccommodationType::firstOrCreate([
                    'property_category_id' => $categoryId,
                    'name' => $type['name'],
                ], [
                    'description' => $type['description'],
                ]);
            }
        }

        // Create countries
        $india = Country::firstOrCreate(['code' => 'IN'], ['name' => 'India']);
        $usa = Country::firstOrCreate(['code' => 'US'], ['name' => 'United States']);
        $uk = Country::firstOrCreate(['code' => 'GB'], ['name' => 'United Kingdom']);

        // Create states for India
        $kerala = State::firstOrCreate(['country_id' => $india->id, 'name' => 'Kerala'], ['code' => 'KL']);
        $karnataka = State::firstOrCreate(['country_id' => $india->id, 'name' => 'Karnataka'], ['code' => 'KA']);
        $tamilnadu = State::firstOrCreate(['country_id' => $india->id, 'name' => 'Tamil Nadu'], ['code' => 'TN']);
        $maharashtra = State::firstOrCreate(['country_id' => $india->id, 'name' => 'Maharashtra'], ['code' => 'MH']);
        $goa = State::firstOrCreate(['country_id' => $india->id, 'name' => 'Goa'], ['code' => 'GA']);

        // Kerala Districts
        $wayanad = District::firstOrCreate(['state_id' => $kerala->id, 'name' => 'Wayanad']);
        $kochi = District::firstOrCreate(['state_id' => $kerala->id, 'name' => 'Ernakulam']);
        $trivandrum = District::firstOrCreate(['state_id' => $kerala->id, 'name' => 'Thiruvananthapuram']);
        $kozhikode = District::firstOrCreate(['state_id' => $kerala->id, 'name' => 'Kozhikode']);

        // Karnataka Districts
        $bangalore = District::firstOrCreate(['state_id' => $karnataka->id, 'name' => 'Bangalore Urban']);
        $mysore = District::firstOrCreate(['state_id' => $karnataka->id, 'name' => 'Mysuru']);
        $coorg = District::firstOrCreate(['state_id' => $karnataka->id, 'name' => 'Kodagu']);

        // Tamil Nadu Districts
        $chennai = District::firstOrCreate(['state_id' => $tamilnadu->id, 'name' => 'Chennai']);
        $ooty = District::firstOrCreate(['state_id' => $tamilnadu->id, 'name' => 'Nilgiris']);

        // Maharashtra Districts
        $mumbai = District::firstOrCreate(['state_id' => $maharashtra->id, 'name' => 'Mumbai']);
        $pune = District::firstOrCreate(['state_id' => $maharashtra->id, 'name' => 'Pune']);

        // Goa Districts
        $northgoa = District::firstOrCreate(['state_id' => $goa->id, 'name' => 'North Goa']);
        $southgoa = District::firstOrCreate(['state_id' => $goa->id, 'name' => 'South Goa']);

        // Cities in Wayanad
        $kalpetta = City::firstOrCreate(['district_id' => $wayanad->id, 'name' => 'Kalpetta']);
        $sultanbathery = City::firstOrCreate(['district_id' => $wayanad->id, 'name' => 'Sultan Bathery']);
        $mananthavady = City::firstOrCreate(['district_id' => $wayanad->id, 'name' => 'Mananthavady']);
        $vythiri = City::firstOrCreate(['district_id' => $wayanad->id, 'name' => 'Vythiri']);

        // Cities in Ernakulam
        $kochi_city = City::firstOrCreate(['district_id' => $kochi->id, 'name' => 'Kochi']);
        $munnar = City::firstOrCreate(['district_id' => $kochi->id, 'name' => 'Munnar']);

        // Cities in Bangalore
        $bangalore_city = City::firstOrCreate(['district_id' => $bangalore->id, 'name' => 'Bangalore']);
        
        // Cities in North Goa
        $panaji = City::firstOrCreate(['district_id' => $northgoa->id, 'name' => 'Panaji']);
        $calangute = City::firstOrCreate(['district_id' => $northgoa->id, 'name' => 'Calangute']);

        // Pincodes for Wayanad
        Pincode::firstOrCreate(['city_id' => $kalpetta->id, 'code' => '673121']);
        Pincode::firstOrCreate(['city_id' => $kalpetta->id, 'code' => '673122']);
        Pincode::firstOrCreate(['city_id' => $sultanbathery->id, 'code' => '673592']);
        Pincode::firstOrCreate(['city_id' => $sultanbathery->id, 'code' => '673593']);
        Pincode::firstOrCreate(['city_id' => $mananthavady->id, 'code' => '670645']);
        Pincode::firstOrCreate(['city_id' => $mananthavady->id, 'code' => '670646']);
        Pincode::firstOrCreate(['city_id' => $vythiri->id, 'code' => '673576']);
        Pincode::firstOrCreate(['city_id' => $vythiri->id, 'code' => '673577']);

        // Pincodes for Kochi
        Pincode::firstOrCreate(['city_id' => $kochi_city->id, 'code' => '682001']);
        Pincode::firstOrCreate(['city_id' => $kochi_city->id, 'code' => '682016']);
        Pincode::firstOrCreate(['city_id' => $munnar->id, 'code' => '685612']);

        // Pincodes for Bangalore
        Pincode::firstOrCreate(['city_id' => $bangalore_city->id, 'code' => '560001']);
        Pincode::firstOrCreate(['city_id' => $bangalore_city->id, 'code' => '560025']);

        // Pincodes for Goa
        Pincode::firstOrCreate(['city_id' => $panaji->id, 'code' => '403001']);
        Pincode::firstOrCreate(['city_id' => $calangute->id, 'code' => '403516']);

        // Create amenities
        $amenities = [
            ['name' => 'WiFi', 'description' => 'Free wireless internet', 'icon' => 'wifi'],
            ['name' => 'Parking', 'description' => 'Free parking available', 'icon' => 'car'],
            ['name' => 'Swimming Pool', 'description' => 'Swimming pool', 'icon' => 'pool'],
            ['name' => 'Gym/Fitness Center', 'description' => 'Fitness center', 'icon' => 'gym'],
            ['name' => 'Restaurant', 'description' => 'On-site dining', 'icon' => 'restaurant'],
            ['name' => 'Spa', 'description' => 'Spa services', 'icon' => 'spa'],
            ['name' => 'Air Conditioning', 'description' => 'Climate control', 'icon' => 'ac'],
            ['name' => 'Room Service', 'description' => '24/7 room service', 'icon' => 'service'],
            ['name' => 'Laundry Service', 'description' => 'Laundry facilities', 'icon' => 'laundry'],
            ['name' => 'Concierge', 'description' => 'Concierge services', 'icon' => 'concierge'],
            ['name' => 'Business Center', 'description' => 'Business facilities', 'icon' => 'business'],
            ['name' => 'Pet Friendly', 'description' => 'Pets allowed', 'icon' => 'pet'],
            ['name' => 'Balcony/Terrace', 'description' => 'Private balcony', 'icon' => 'balcony'],
            ['name' => 'Kitchen', 'description' => 'Kitchenette available', 'icon' => 'kitchen'],
            ['name' => 'TV/Cable', 'description' => 'Television with cable', 'icon' => 'tv'],
            ['name' => 'Mini Bar', 'description' => 'In-room mini bar', 'icon' => 'minibar'],
            ['name' => 'Safe/Locker', 'description' => 'Security safe', 'icon' => 'safe'],
            ['name' => 'Elevator', 'description' => 'Elevator access', 'icon' => 'elevator'],
            ['name' => 'Garden/Lawn', 'description' => 'Garden area', 'icon' => 'garden'],
            ['name' => 'Conference Room', 'description' => 'Meeting facilities', 'icon' => 'conference']
        ];
        
        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(['name' => $amenity['name']], $amenity);
        }

        // Create admin user
        User::firstOrCreate(
            ['mobile_number' => '1111111111'],
            [
                'name' => 'Admin User',
                'pin_hash' => Hash::make('0000'),
                'email' => 'admin@example.com',
                'is_admin' => true,
            ]
        );

        // Create demo user
        User::firstOrCreate(
            ['mobile_number' => '9876543210'],
            [
                'name' => 'Niyas',
                'pin_hash' => Hash::make('1234'),
                'email' => 'demo@example.com',
            ]
        );
    }
}
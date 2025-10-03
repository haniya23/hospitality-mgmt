<?php

namespace Database\Seeders;

use App\Models\PropertyCategory;
use App\Models\PredefinedAccommodationType;
use App\Models\User;
use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BasicDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create property categories (ordered by popularity/common usage)
        $hotel = PropertyCategory::firstOrCreate(['name' => 'Hotel'], ['description' => 'Traditional hotel accommodation']);
        $apartment = PropertyCategory::firstOrCreate(['name' => 'Apartment'], ['description' => 'Serviced apartments and flats']);
        $resort = PropertyCategory::firstOrCreate(['name' => 'Resort'], ['description' => 'Resort with recreational facilities']);
        $villa = PropertyCategory::firstOrCreate(['name' => 'Villa'], ['description' => 'Private villa accommodation']);
        $homestay = PropertyCategory::firstOrCreate(['name' => 'Homestay'], ['description' => 'Home-based accommodation']);
        $heritage = PropertyCategory::firstOrCreate(['name' => 'Heritage'], ['description' => 'Heritage properties with historical significance']);
        $boutique = PropertyCategory::firstOrCreate(['name' => 'Boutique Hotel'], ['description' => 'Small, stylish hotels with unique designs']);
        $hostel = PropertyCategory::firstOrCreate(['name' => 'Hostel'], ['description' => 'Budget-friendly shared accommodation']);
        $bed_and_breakfast = PropertyCategory::firstOrCreate(['name' => 'Bed & Breakfast'], ['description' => 'Small establishments with breakfast included']);
        $guest_house = PropertyCategory::firstOrCreate(['name' => 'Guest House'], ['description' => 'Private homes converted to guest accommodation']);
        $motel = PropertyCategory::firstOrCreate(['name' => 'Motel'], ['description' => 'Motorist-oriented accommodation with parking']);
        $extended_stay = PropertyCategory::firstOrCreate(['name' => 'Extended Stay'], ['description' => 'Hotels designed for longer stays']);
        $business_hotel = PropertyCategory::firstOrCreate(['name' => 'Business Hotel'], ['description' => 'Hotels with business facilities']);
        $spa_hotel = PropertyCategory::firstOrCreate(['name' => 'Spa Hotel'], ['description' => 'Hotels focused on wellness and spa treatments']);
        $eco_lodge = PropertyCategory::firstOrCreate(['name' => 'Eco Lodge'], ['description' => 'Environmentally friendly accommodations']);
        $unique_stays = PropertyCategory::firstOrCreate(['name' => 'Unique Stays'], ['description' => 'Unusual and themed accommodations']);

        // Create accommodation types for each property category (ordered by popularity/common usage)
        $accommodationTypes = [
            // HOTEL - Most common worldwide
            $hotel->id => [
                ['name' => 'Standard Room', 'description' => 'Basic hotel room with essential amenities'],
                ['name' => 'Superior Room', 'description' => 'Enhanced room with better amenities and views'],
                ['name' => 'Deluxe Room', 'description' => 'Spacious room with premium amenities'],
                ['name' => 'Executive Room', 'description' => 'Business-class room with work facilities'],
                ['name' => 'Junior Suite', 'description' => 'Room with separate sitting area'],
                ['name' => 'Executive Suite', 'description' => 'Luxury suite with separate living and bedroom'],
                ['name' => 'Presidential Suite', 'description' => 'Ultimate luxury suite with premium services'],
                ['name' => 'Family Room', 'description' => 'Room designed for families with children'],
                ['name' => 'Twin Room', 'description' => 'Room with two separate beds'],
                ['name' => 'King Room', 'description' => 'Room with king-size bed'],
                ['name' => 'Queen Room', 'description' => 'Room with queen-size bed'],
                ['name' => 'Accessible Room', 'description' => 'Room designed for guests with disabilities'],
                ['name' => 'Connecting Rooms', 'description' => 'Two rooms with connecting door'],
                ['name' => 'Adjoining Rooms', 'description' => 'Two separate rooms side by side'],
                ['name' => 'Custom', 'description' => 'Custom hotel accommodation type'],
            ],
            
            // APARTMENT - Very popular for longer stays
            $apartment->id => [
                ['name' => 'Studio Apartment', 'description' => 'Single room with kitchenette and bathroom'],
                ['name' => '1 Bedroom Apartment', 'description' => 'One bedroom with living area and kitchen'],
                ['name' => '2 Bedroom Apartment', 'description' => 'Two bedrooms with living area and kitchen'],
                ['name' => '3 Bedroom Apartment', 'description' => 'Three bedrooms with living area and kitchen'],
                ['name' => 'Penthouse', 'description' => 'Luxury top-floor apartment'],
                ['name' => 'Serviced Apartment', 'description' => 'Apartment with hotel-like services'],
                ['name' => 'Loft', 'description' => 'Open-plan apartment in converted building'],
                ['name' => 'Duplex', 'description' => 'Two-story apartment unit'],
                ['name' => 'Garden Apartment', 'description' => 'Ground-floor apartment with garden access'],
                ['name' => 'Custom', 'description' => 'Custom apartment type'],
            ],
            
            // RESORT - Popular for leisure travel
            $resort->id => [
                ['name' => 'Resort Room', 'description' => 'Standard resort accommodation'],
                ['name' => 'Garden View Room', 'description' => 'Room with garden or landscape views'],
                ['name' => 'Ocean View Room', 'description' => 'Room with ocean or sea views'],
                ['name' => 'Mountain View Room', 'description' => 'Room with mountain or scenic views'],
                ['name' => 'Pool View Room', 'description' => 'Room overlooking the swimming pool'],
                ['name' => 'Beachfront Room', 'description' => 'Room with direct beach access'],
                ['name' => 'Pool Villa', 'description' => 'Private villa with swimming pool'],
                ['name' => 'Beach Villa', 'description' => 'Villa with direct beach access'],
                ['name' => 'Garden Villa', 'description' => 'Villa surrounded by gardens'],
                ['name' => 'Water Villa', 'description' => 'Villa built over water'],
                ['name' => 'Overwater Bungalow', 'description' => 'Luxury bungalow built over water'],
                ['name' => 'Cottage', 'description' => 'Private cottage accommodation'],
                ['name' => 'Bungalow', 'description' => 'Single-story standalone accommodation'],
                ['name' => 'Custom', 'description' => 'Custom resort accommodation type'],
            ],
            
            // VILLA - Luxury accommodation
            $villa->id => [
                ['name' => 'Entire Villa', 'description' => 'Complete villa for exclusive use'],
                ['name' => 'Villa Suite', 'description' => 'Luxury suite within villa complex'],
                ['name' => 'Private Villa', 'description' => 'Exclusive private villa'],
                ['name' => 'Villa with Pool', 'description' => 'Villa with private swimming pool'],
                ['name' => 'Beach Villa', 'description' => 'Villa with direct beach access'],
                ['name' => 'Mountain Villa', 'description' => 'Villa with mountain views'],
                ['name' => 'Garden Villa', 'description' => 'Villa surrounded by gardens'],
                ['name' => 'Luxury Villa', 'description' => 'High-end villa with premium amenities'],
                ['name' => 'Family Villa', 'description' => 'Villa designed for families'],
                ['name' => 'Custom', 'description' => 'Custom villa type'],
            ],
            
            // HOMESTAY - Growing in popularity
            $homestay->id => [
                ['name' => 'Entire House', 'description' => 'Complete house for exclusive use'],
                ['name' => 'Private Room', 'description' => 'Private bedroom with shared common areas'],
                ['name' => 'Shared Room', 'description' => 'Room shared with other guests'],
                ['name' => 'Family Room', 'description' => 'Room suitable for families'],
                ['name' => 'Single Room', 'description' => 'Room for single occupancy'],
                ['name' => 'Double Room', 'description' => 'Room with double bed'],
                ['name' => 'Guest Room', 'description' => 'Dedicated guest bedroom'],
                ['name' => 'Custom', 'description' => 'Custom homestay type'],
            ],
            
            // HERITAGE - Unique and cultural
            $heritage->id => [
                ['name' => 'Heritage Suite', 'description' => 'Luxurious suite in heritage building'],
                ['name' => 'Palace Room', 'description' => 'Royal themed accommodation'],
                ['name' => 'Courtyard Room', 'description' => 'Room overlooking heritage courtyard'],
                ['name' => 'Historical Room', 'description' => 'Room with historical significance'],
                ['name' => 'Castle Suite', 'description' => 'Suite in castle or fortress'],
                ['name' => 'Monastery Room', 'description' => 'Room in converted monastery'],
                ['name' => 'Heritage Villa', 'description' => 'Villa with heritage architecture'],
                ['name' => 'Custom', 'description' => 'Custom heritage accommodation type'],
            ],
            
            // BOUTIQUE HOTEL - Trendy and unique
            $boutique->id => [
                ['name' => 'Boutique Room', 'description' => 'Stylish room with unique design'],
                ['name' => 'Designer Suite', 'description' => 'Suite with contemporary design'],
                ['name' => 'Art Room', 'description' => 'Room featuring local artwork'],
                ['name' => 'Themed Room', 'description' => 'Room with specific theme or style'],
                ['name' => 'Loft Suite', 'description' => 'Industrial-style loft accommodation'],
                ['name' => 'Custom', 'description' => 'Custom boutique accommodation type'],
            ],
            
            // HOSTEL - Budget accommodation
            $hostel->id => [
                ['name' => 'Dormitory', 'description' => 'Shared room with multiple beds'],
                ['name' => 'Private Room', 'description' => 'Private room in hostel'],
                ['name' => 'Double Room', 'description' => 'Private room with double bed'],
                ['name' => 'Twin Room', 'description' => 'Private room with two beds'],
                ['name' => 'Female Dorm', 'description' => 'Female-only dormitory'],
                ['name' => 'Male Dorm', 'description' => 'Male-only dormitory'],
                ['name' => 'Mixed Dorm', 'description' => 'Mixed gender dormitory'],
                ['name' => 'Custom', 'description' => 'Custom hostel accommodation type'],
            ],
            
            // BED & BREAKFAST - Traditional accommodation
            $bed_and_breakfast->id => [
                ['name' => 'B&B Room', 'description' => 'Standard bed and breakfast room'],
                ['name' => 'En-suite Room', 'description' => 'Room with private bathroom'],
                ['name' => 'Shared Bathroom Room', 'description' => 'Room with shared bathroom'],
                ['name' => 'Family Room', 'description' => 'Room suitable for families'],
                ['name' => 'Single Room', 'description' => 'Room for single occupancy'],
                ['name' => 'Custom', 'description' => 'Custom B&B accommodation type'],
            ],
            
            // GUEST HOUSE - Local accommodation
            $guest_house->id => [
                ['name' => 'Guest Room', 'description' => 'Standard guest house room'],
                ['name' => 'Private Room', 'description' => 'Private room with shared facilities'],
                ['name' => 'Family Room', 'description' => 'Room suitable for families'],
                ['name' => 'Single Room', 'description' => 'Room for single occupancy'],
                ['name' => 'Custom', 'description' => 'Custom guest house type'],
            ],
            
            // MOTEL - Roadside accommodation
            $motel->id => [
                ['name' => 'Standard Room', 'description' => 'Basic motel room'],
                ['name' => 'King Room', 'description' => 'Room with king-size bed'],
                ['name' => 'Queen Room', 'description' => 'Room with queen-size bed'],
                ['name' => 'Family Room', 'description' => 'Room suitable for families'],
                ['name' => 'Suite', 'description' => 'Motel suite with extra space'],
                ['name' => 'Custom', 'description' => 'Custom motel accommodation type'],
            ],
            
            // EXTENDED STAY - Long-term accommodation
            $extended_stay->id => [
                ['name' => 'Extended Stay Room', 'description' => 'Room designed for long-term stays'],
                ['name' => 'Suite with Kitchenette', 'description' => 'Suite with cooking facilities'],
                ['name' => 'One Bedroom Suite', 'description' => 'One bedroom with kitchenette'],
                ['name' => 'Two Bedroom Suite', 'description' => 'Two bedrooms with kitchenette'],
                ['name' => 'Studio Suite', 'description' => 'Studio with kitchenette'],
                ['name' => 'Custom', 'description' => 'Custom extended stay type'],
            ],
            
            // BUSINESS HOTEL - Corporate accommodation
            $business_hotel->id => [
                ['name' => 'Executive Room', 'description' => 'Room with business amenities'],
                ['name' => 'Business Suite', 'description' => 'Suite with meeting facilities'],
                ['name' => 'Club Room', 'description' => 'Room with club lounge access'],
                ['name' => 'Executive Floor Room', 'description' => 'Room on executive floor'],
                ['name' => 'Custom', 'description' => 'Custom business hotel type'],
            ],
            
            // SPA HOTEL - Wellness accommodation
            $spa_hotel->id => [
                ['name' => 'Spa Room', 'description' => 'Room with spa amenities'],
                ['name' => 'Wellness Suite', 'description' => 'Suite with wellness features'],
                ['name' => 'Spa Villa', 'description' => 'Villa with private spa facilities'],
                ['name' => 'Treatment Room', 'description' => 'Room with spa treatment area'],
                ['name' => 'Custom', 'description' => 'Custom spa hotel type'],
            ],
            
            // ECO LODGE - Sustainable accommodation
            $eco_lodge->id => [
                ['name' => 'Eco Room', 'description' => 'Environmentally friendly room'],
                ['name' => 'Nature Villa', 'description' => 'Villa with sustainable features'],
                ['name' => 'Tree House', 'description' => 'Accommodation built in trees'],
                ['name' => 'Earth Lodge', 'description' => 'Lodge built with natural materials'],
                ['name' => 'Custom', 'description' => 'Custom eco lodge type'],
            ],
            
            // UNIQUE STAYS - Specialized accommodation
            $unique_stays->id => [
                ['name' => 'Tree House', 'description' => 'Unique tree house experience'],
                ['name' => 'Cave Room', 'description' => 'Room built in natural cave'],
                ['name' => 'Yurt', 'description' => 'Traditional tent accommodation'],
                ['name' => 'Capsule Hotel', 'description' => 'Compact capsule accommodation'],
                ['name' => 'Ice Hotel Room', 'description' => 'Room made of ice and snow'],
                ['name' => 'Houseboat', 'description' => 'Accommodation on water'],
                ['name' => 'Glamping Tent', 'description' => 'Luxury camping tent'],
                ['name' => 'Safari Tent', 'description' => 'Tent in wildlife reserve'],
                ['name' => 'Railway Carriage', 'description' => 'Converted train carriage'],
                ['name' => 'Airplane Suite', 'description' => 'Suite in converted airplane'],
                ['name' => 'Lighthouse Room', 'description' => 'Room in converted lighthouse'],
                ['name' => 'Castle Room', 'description' => 'Room in castle'],
                ['name' => 'Monastery Cell', 'description' => 'Room in monastery'],
                ['name' => 'Igloo', 'description' => 'Traditional ice accommodation'],
                ['name' => 'Custom', 'description' => 'Custom unique accommodation type'],
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
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'mobile_number' => '1111111111',
                'pin_hash' => Hash::make('0000'),
                'password' => Hash::make('password'),
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
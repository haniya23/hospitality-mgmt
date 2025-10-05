<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyLocation;
use App\Models\PropertyAccommodation;
use App\Models\PredefinedAccommodationType;
use App\Models\B2bPartner;
use App\Models\Guest;
use App\Models\Role;
use App\Models\StaffAssignment;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
use App\Models\Amenity;
use Illuminate\Support\Facades\Hash;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create countries, states, districts, cities, pincodes first
        $this->createLocationData();
        
        // Create property categories and amenities (only if they don't exist)
        $this->createPropertyData();
        
        // Create owner user
        $owner = $this->createOwner();
        
        // Create properties for the owner
        $properties = $this->createProperties($owner);
        
        // Create accommodations for each property
        $this->createAccommodations($properties);
        
        // Create B2B partners
        $this->createB2bPartners($owner);
        
        // Create some staff for the properties
        $this->createStaff($properties);
        
        // Create some guests/customers
        $this->createGuests();
        
        $this->command->info('System seeded successfully!');
        $this->command->info('Owner: ' . $owner->name . ' (' . $owner->mobile_number . ')');
        $this->command->info('Properties created: ' . $properties->count());
        $this->command->info('B2B Partners created: 3');
        $this->command->info('Staff members created: 6');
        $this->command->info('Guests created: 10');
    }
    
    private function createLocationData()
    {
        $this->command->info('Creating location data...');
        
        // Create India
        $india = Country::firstOrCreate([
            'name' => 'India',
            'code' => 'IN'
        ]);
        
        // Create Kerala
        $kerala = State::firstOrCreate([
            'name' => 'Kerala',
            'country_id' => $india->id,
            'code' => 'KL'
        ]);
        
        // Create Ernakulam district
        $ernakulam = District::firstOrCreate([
            'name' => 'Ernakulam',
            'state_id' => $kerala->id
        ]);
        
        // Create Kochi city
        $kochi = City::firstOrCreate([
            'name' => 'Kochi',
            'district_id' => $ernakulam->id
        ]);
        
        // Create some pincodes
        $pincodes = ['682001', '682002', '682003', '682004', '682005'];
        foreach ($pincodes as $pincode) {
            Pincode::firstOrCreate([
                'code' => $pincode,
                'city_id' => $kochi->id
            ]);
        }
        
        // Store location data for later use
        $this->locationData = [
            'country' => $india,
            'state' => $kerala,
            'district' => $ernakulam,
            'city' => $kochi,
            'pincode' => Pincode::where('city_id', $kochi->id)->first()
        ];
    }
    
    private function createPropertyData()
    {
        $this->command->info('Using existing property categories and amenities...');
        
        // Property categories and amenities should already exist from BasicDataSeeder
        // We'll just verify they exist and create accommodation types if needed
        
        // Create accommodation types (these might not exist)
        $accommodationTypes = [
            ['name' => 'Standard Room', 'description' => 'Basic accommodation'],
            ['name' => 'Deluxe Room', 'description' => 'Enhanced accommodation'],
            ['name' => 'Suite', 'description' => 'Premium accommodation'],
            ['name' => 'Villa', 'description' => 'Luxury villa'],
            ['name' => 'Apartment', 'description' => 'Serviced apartment'],
        ];
        
        // Get a property category to associate with (use the first one)
        $defaultCategory = PropertyCategory::first();
        if ($defaultCategory) {
            foreach ($accommodationTypes as $type) {
                PredefinedAccommodationType::firstOrCreate([
                    'name' => $type['name'],
                    'property_category_id' => $defaultCategory->id,
                ], $type);
            }
        }
    }
    
    private function createOwner()
    {
        $this->command->info('Creating owner...');
        
        return User::firstOrCreate(
            ['mobile_number' => '9876543210'],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@hospitality.com',
                'password' => Hash::make('password123'),
                'pin_hash' => Hash::make('1234'),
                'is_active' => true,
                'is_admin' => false,
                'user_type' => 'owner',
                'is_staff' => false,
                'subscription_status' => 'professional',
                'billing_cycle' => 'monthly',
            ]
        );
    }
    
    private function createProperties($owner)
    {
        $this->command->info('Creating properties...');
        
        $properties = collect();
        $propertyData = [
            [
                'name' => 'Ocean View Resort',
                'description' => 'Luxury beachfront resort with stunning ocean views',
                'category' => 'Resort',
            ],
            [
                'name' => 'City Center Hotel',
                'description' => 'Modern hotel in the heart of the city',
                'category' => 'Hotel',
            ],
            [
                'name' => 'Cozy Homestay',
                'description' => 'Traditional homestay with local charm',
                'category' => 'Homestay',
            ],
        ];
        
        foreach ($propertyData as $index => $data) {
            $category = PropertyCategory::where('name', $data['category'])->first();
            
            $property = Property::create([
                'owner_id' => $owner->id,
                'property_category_id' => $category->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => 'active',
                'wizard_step_completed' => 5,
                'approved_at' => now(),
                'approved_by' => 1, // Assuming admin user ID 1
            ]);
            
            // Create property location
            PropertyLocation::create([
                'property_id' => $property->id,
                'country_id' => $this->locationData['country']->id,
                'state_id' => $this->locationData['state']->id,
                'district_id' => $this->locationData['district']->id,
                'city_id' => $this->locationData['city']->id,
                'pincode_id' => $this->locationData['pincode']->id,
                'address' => $index === 0 ? 'Marine Drive, Kochi' : 
                           ($index === 1 ? 'MG Road, Kochi' : 'Fort Kochi, Kochi'),
                'latitude' => 9.9312 + ($index * 0.01),
                'longitude' => 76.2673 + ($index * 0.01),
            ]);
            
            $properties->push($property);
        }
        
        return $properties;
    }
    
    private function createAccommodations($properties)
    {
        $this->command->info('Creating accommodations...');
        
        $accommodationTypes = PredefinedAccommodationType::all();
        $amenities = Amenity::all();
        
        $accommodationData = [
            // Ocean View Resort - 2 accommodations
            [
                'name' => 'Ocean View Suite',
                'type' => 'Suite',
                'description' => 'Luxury suite with panoramic ocean views',
                'max_occupancy' => 4,
                'base_rate' => 15000,
                'amenities' => ['WiFi', 'Air Conditioning', 'Swimming Pool', 'Room Service', 'Spa'],
            ],
            [
                'name' => 'Deluxe Ocean Room',
                'type' => 'Deluxe Room',
                'description' => 'Comfortable room with ocean views',
                'max_occupancy' => 2,
                'base_rate' => 8000,
                'amenities' => ['WiFi', 'Air Conditioning', 'Swimming Pool', 'Room Service'],
            ],
            // City Center Hotel - 2 accommodations
            [
                'name' => 'Executive Suite',
                'type' => 'Suite',
                'description' => 'Spacious suite for business travelers',
                'max_occupancy' => 3,
                'base_rate' => 12000,
                'amenities' => ['WiFi', 'Air Conditioning', 'Restaurant', 'Gym', 'Laundry Service'],
            ],
            [
                'name' => 'Standard Business Room',
                'type' => 'Standard Room',
                'description' => 'Comfortable room for business stays',
                'max_occupancy' => 2,
                'base_rate' => 6000,
                'amenities' => ['WiFi', 'Air Conditioning', 'Restaurant'],
            ],
            // Cozy Homestay - 1 accommodation
            [
                'name' => 'Traditional Room',
                'type' => 'Standard Room',
                'description' => 'Cozy traditional room with local decor',
                'max_occupancy' => 2,
                'base_rate' => 3000,
                'amenities' => ['WiFi', 'Parking'],
            ],
        ];
        
        foreach ($accommodationData as $index => $data) {
            $type = $accommodationTypes->where('name', $data['type'])->first();
            $property = $properties->get($index < 2 ? 0 : ($index < 4 ? 1 : 2));
            
            $accommodation = PropertyAccommodation::create([
                'property_id' => $property->id,
                'predefined_accommodation_type_id' => $type->id,
                'custom_name' => $data['name'],
                'description' => $data['description'],
                'max_occupancy' => $data['max_occupancy'],
                'base_price' => $data['base_rate'],
                'size' => rand(300, 800) / 10, // Random size between 30-80 sq ft
                'features' => json_encode($data['amenities']),
                'is_active' => true,
            ]);
            
            // Attach amenities
            $amenityIds = $amenities->whereIn('name', $data['amenities'])->pluck('id');
            $accommodation->amenities()->attach($amenityIds);
        }
    }
    
    private function createB2bPartners($owner)
    {
        $this->command->info('Creating B2B partners...');
        
        $partners = [
            [
                'partner_name' => 'Travel World Agency',
                'partner_type' => 'Travel Agency',
                'contact_person' => 'Sarah Johnson',
                'mobile_number' => '9876543211',
                'email' => 'sarah@travelworld.com',
                'commission_rate' => 15.00,
                'default_discount_pct' => 10.00,
            ],
            [
                'partner_name' => 'Corporate Solutions Ltd',
                'partner_type' => 'Corporate',
                'contact_person' => 'Michael Chen',
                'mobile_number' => '9876543212',
                'email' => 'michael@corporatesolutions.com',
                'commission_rate' => 12.00,
                'default_discount_pct' => 8.00,
            ],
            [
                'partner_name' => 'Adventure Tours',
                'partner_type' => 'Tour Operator',
                'contact_person' => 'Rajesh Kumar',
                'mobile_number' => '9876543213',
                'email' => 'rajesh@adventuretours.com',
                'commission_rate' => 18.00,
                'default_discount_pct' => 12.00,
            ],
        ];
        
        foreach ($partners as $partnerData) {
            // Create user for the partner
            $partnerUser = User::create([
                'name' => $partnerData['contact_person'],
                'mobile_number' => $partnerData['mobile_number'],
                'email' => $partnerData['email'],
                'password' => Hash::make('password123'),
                'pin_hash' => Hash::make('1234'),
                'is_active' => true,
                'is_admin' => false,
                'user_type' => 'b2b',
                'is_staff' => false,
            ]);
            
            // Create B2B partner
            B2bPartner::create([
                'partner_name' => $partnerData['partner_name'],
                'partner_type' => $partnerData['partner_type'],
                'contact_user_id' => $partnerUser->id,
                'email' => $partnerData['email'],
                'commission_rate' => $partnerData['commission_rate'],
                'default_discount_pct' => $partnerData['default_discount_pct'],
                'requested_by' => $owner->id,
                'partnership_accepted_at' => now(),
                'status' => 'active',
            ]);
        }
    }
    
    private function createStaff($properties)
    {
        $this->command->info('Creating staff members...');
        
        $staffData = [
            [
                'name' => 'Alice Manager',
                'mobile_number' => '9876543220',
                'role' => 'Manager',
                'property_index' => 0,
                'booking_access' => true,
                'guest_service_access' => true,
            ],
            [
                'name' => 'Bob Receptionist',
                'mobile_number' => '9876543221',
                'role' => 'Receptionist',
                'property_index' => 0,
                'booking_access' => false,
                'guest_service_access' => true,
            ],
            [
                'name' => 'Charlie Housekeeper',
                'mobile_number' => '9876543222',
                'role' => 'Housekeeper',
                'property_index' => 1,
                'booking_access' => false,
                'guest_service_access' => false,
            ],
            [
                'name' => 'Diana Manager',
                'mobile_number' => '9876543223',
                'role' => 'Manager',
                'property_index' => 1,
                'booking_access' => true,
                'guest_service_access' => true,
            ],
            [
                'name' => 'Eve Concierge',
                'mobile_number' => '9876543224',
                'role' => 'Concierge',
                'property_index' => 2,
                'booking_access' => false,
                'guest_service_access' => true,
            ],
            [
                'name' => 'Frank Caretaker',
                'mobile_number' => '9876543225',
                'role' => 'Caretaker',
                'property_index' => 2,
                'booking_access' => false,
                'guest_service_access' => false,
            ],
        ];
        
        foreach ($staffData as $staff) {
            // Create user for staff
            $staffUser = User::create([
                'name' => $staff['name'],
                'mobile_number' => $staff['mobile_number'],
                'email' => strtolower(str_replace(' ', '.', $staff['name'])) . '@staff.com',
                'password' => Hash::make('password123'),
                'pin_hash' => Hash::make('1234'),
                'is_active' => true,
                'is_admin' => false,
                'user_type' => 'staff',
                'is_staff' => true,
            ]);
            
            // Get or create role
            $role = Role::firstOrCreate([
                'name' => $staff['role'],
                'property_id' => $properties->get($staff['property_index'])->id,
            ], [
                'description' => ucfirst($staff['role']) . ' role',
                'is_active' => true,
            ]);
            
            // Create staff assignment
            StaffAssignment::create([
                'user_id' => $staffUser->id,
                'property_id' => $properties->get($staff['property_index'])->id,
                'role_id' => $role->id,
                'status' => 'active',
                'booking_access' => $staff['booking_access'],
                'guest_service_access' => $staff['guest_service_access'],
                'start_date' => now()->subDays(30),
                'end_date' => now()->addYear(),
            ]);
        }
    }
    
    private function createGuests()
    {
        $this->command->info('Creating guests...');
        
        $guests = [
            ['name' => 'Robert Smith', 'email' => 'robert@email.com', 'mobile_number' => '9876543230'],
            ['name' => 'Emily Davis', 'email' => 'emily@email.com', 'mobile_number' => '9876543231'],
            ['name' => 'David Wilson', 'email' => 'david@email.com', 'mobile_number' => '9876543232'],
            ['name' => 'Lisa Brown', 'email' => 'lisa@email.com', 'mobile_number' => '9876543233'],
            ['name' => 'James Taylor', 'email' => 'james@email.com', 'mobile_number' => '9876543234'],
            ['name' => 'Maria Garcia', 'email' => 'maria@email.com', 'mobile_number' => '9876543235'],
            ['name' => 'John Martinez', 'email' => 'john.m@email.com', 'mobile_number' => '9876543236'],
            ['name' => 'Sarah Anderson', 'email' => 'sarah@email.com', 'mobile_number' => '9876543237'],
            ['name' => 'Michael Thomas', 'email' => 'michael@email.com', 'mobile_number' => '9876543238'],
            ['name' => 'Jennifer Jackson', 'email' => 'jennifer@email.com', 'mobile_number' => '9876543239'],
        ];
        
        foreach ($guests as $guest) {
            Guest::create([
                'name' => $guest['name'],
                'email' => $guest['email'],
                'mobile_number' => $guest['mobile_number'],
                'loyalty_points' => rand(0, 1000),
                'total_stays' => rand(0, 10),
                'last_stay_at' => now()->subDays(rand(1, 90)),
            ]);
        }
    }
}
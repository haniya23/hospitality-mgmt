<?php

namespace Database\Seeders;

use App\Models\PropertyAccommodation;
use App\Models\Property;
use App\Models\PredefinedAccommodationType;
use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        // Check if accommodations already exist
        if (PropertyAccommodation::count() >= 20) {
            $this->command->info('Accommodations already seeded. Skipping...');
            return;
        }

        // Get existing data
        $properties = Property::all();
        $accommodationTypes = PredefinedAccommodationType::all();
        $amenities = Amenity::all();

        if ($properties->isEmpty()) {
            $this->command->warn('No properties found. Please run PropertySeeder first.');
            return;
        }

        // Comprehensive accommodation names organized by category popularity
        $accommodationNames = [
            // Hotel types (most common)
            'Standard Room', 'Superior Room', 'Deluxe Room', 'Executive Room', 'Junior Suite', 'Executive Suite',
            'Presidential Suite', 'Family Room', 'Twin Room', 'King Room', 'Queen Room', 'Accessible Room',
            
            // Apartment types
            'Studio Apartment', '1 Bedroom Apartment', '2 Bedroom Apartment', '3 Bedroom Apartment', 'Penthouse',
            'Serviced Apartment', 'Loft', 'Duplex', 'Garden Apartment',
            
            // Resort types
            'Resort Room', 'Garden View Room', 'Ocean View Room', 'Mountain View Room', 'Pool View Room',
            'Beachfront Room', 'Pool Villa', 'Beach Villa', 'Garden Villa', 'Water Villa', 'Overwater Bungalow',
            'Cottage', 'Bungalow',
            
            // Villa types
            'Entire Villa', 'Villa Suite', 'Private Villa', 'Villa with Pool', 'Beach Villa', 'Mountain Villa',
            'Garden Villa', 'Luxury Villa', 'Family Villa',
            
            // Homestay types
            'Entire House', 'Private Room', 'Shared Room', 'Family Room', 'Single Room', 'Double Room',
            'Guest Room',
            
            // Heritage types
            'Heritage Suite', 'Palace Room', 'Courtyard Room', 'Historical Room', 'Castle Suite',
            'Monastery Room', 'Heritage Villa',
            
            // Boutique types
            'Boutique Room', 'Designer Suite', 'Art Room', 'Themed Room', 'Loft Suite',
            
            // Hostel types
            'Dormitory', 'Private Room', 'Double Room', 'Twin Room', 'Female Dorm', 'Male Dorm', 'Mixed Dorm',
            
            // B&B types
            'B&B Room', 'En-suite Room', 'Shared Bathroom Room', 'Family Room', 'Single Room',
            
            // Guest House types
            'Guest Room', 'Private Room', 'Family Room', 'Single Room',
            
            // Motel types
            'Standard Room', 'King Room', 'Queen Room', 'Family Room', 'Suite',
            
            // Extended Stay types
            'Extended Stay Room', 'Suite with Kitchenette', 'One Bedroom Suite', 'Two Bedroom Suite', 'Studio Suite',
            
            // Business Hotel types
            'Executive Room', 'Business Suite', 'Club Room', 'Executive Floor Room',
            
            // Spa Hotel types
            'Spa Room', 'Wellness Suite', 'Spa Villa', 'Treatment Room',
            
            // Eco Lodge types
            'Eco Room', 'Nature Villa', 'Tree House', 'Earth Lodge',
            
            // Unique Stay types
            'Tree House', 'Cave Room', 'Yurt', 'Capsule Hotel', 'Ice Hotel Room', 'Houseboat',
            'Glamping Tent', 'Safari Tent', 'Railway Carriage', 'Airplane Suite', 'Lighthouse Room',
            'Castle Room', 'Monastery Cell', 'Igloo'
        ];

        $descriptions = [
            // Hotel descriptions
            'Comfortable room with essential amenities and modern furnishings.',
            'Enhanced room with upgraded amenities and better views.',
            'Spacious room with premium amenities and elegant décor.',
            'Business-class room with work desk and high-speed internet.',
            'Suite with separate sitting area and enhanced amenities.',
            'Luxury suite with separate living and bedroom areas.',
            'Ultimate luxury suite with premium services and panoramic views.',
            'Family-friendly room with extra space and child amenities.',
            'Room with two separate beds for flexible sleeping arrangements.',
            'Room featuring a comfortable king-size bed.',
            'Room featuring a comfortable queen-size bed.',
            'Accessible room designed for guests with mobility needs.',
            
            // Apartment descriptions
            'Single room with kitchenette and private bathroom.',
            'One bedroom with living area and fully equipped kitchen.',
            'Two bedrooms with living area and fully equipped kitchen.',
            'Three bedrooms with living area and fully equipped kitchen.',
            'Luxury top-floor apartment with premium amenities.',
            'Apartment with hotel-like services and facilities.',
            'Open-plan apartment in converted industrial building.',
            'Two-story apartment unit with spacious layout.',
            'Ground-floor apartment with private garden access.',
            
            // Resort descriptions
            'Standard resort accommodation with recreational facilities.',
            'Room with beautiful garden or landscape views.',
            'Room with stunning ocean or sea views.',
            'Room with breathtaking mountain or scenic views.',
            'Room overlooking the resort swimming pool.',
            'Room with direct beach access and oceanfront views.',
            'Private villa with swimming pool and outdoor facilities.',
            'Villa with direct beach access and oceanfront views.',
            'Villa surrounded by beautiful gardens and natural landscaping.',
            'Villa built over water with stunning aquatic views.',
            'Luxury bungalow built over crystal-clear waters.',
            'Private cottage with rustic charm and modern amenities.',
            'Single-story standalone accommodation with garden access.',
            
            // Villa descriptions
            'Complete villa for exclusive use with all amenities.',
            'Luxury suite within a villa complex.',
            'Exclusive private villa with personalized service.',
            'Villa with private swimming pool and outdoor facilities.',
            'Villa with direct beach access and oceanfront location.',
            'Villa with stunning mountain views and natural surroundings.',
            'Villa surrounded by beautiful gardens and landscaping.',
            'High-end villa with premium amenities and luxury services.',
            'Villa specifically designed for family accommodation.',
            
            // Homestay descriptions
            'Complete house for exclusive use with local hospitality.',
            'Private bedroom with shared common areas and kitchen.',
            'Room shared with other guests in a friendly atmosphere.',
            'Room suitable for families with children.',
            'Room designed for single occupancy.',
            'Room with a comfortable double bed.',
            'Dedicated guest bedroom with shared facilities.',
            
            // Heritage descriptions
            'Luxurious suite in a beautifully restored heritage building.',
            'Royal themed accommodation with historical charm.',
            'Room overlooking a traditional heritage courtyard.',
            'Room with historical significance and period features.',
            'Suite in a castle or historic fortress.',
            'Room in a converted monastery with spiritual ambiance.',
            'Villa featuring heritage architecture and traditional design.',
            
            // Boutique descriptions
            'Stylish room with unique design and contemporary amenities.',
            'Suite with contemporary design and artistic elements.',
            'Room featuring local artwork and cultural elements.',
            'Room with a specific theme or distinctive style.',
            'Industrial-style loft accommodation with modern amenities.',
            
            // Hostel descriptions
            'Shared room with multiple beds and communal facilities.',
            'Private room within a hostel environment.',
            'Private room with a comfortable double bed.',
            'Private room with two separate beds.',
            'Female-only dormitory with shared facilities.',
            'Male-only dormitory with shared facilities.',
            'Mixed gender dormitory with shared facilities.',
            
            // B&B descriptions
            'Standard bed and breakfast room with home-cooked meals.',
            'Room with private bathroom and breakfast included.',
            'Room with shared bathroom and breakfast included.',
            'Family-friendly room with breakfast included.',
            'Room for single occupancy with breakfast included.',
            
            // Guest House descriptions
            'Standard guest house room with local hospitality.',
            'Private room with shared facilities in a home setting.',
            'Family-friendly room in a guest house.',
            'Single occupancy room in a guest house.',
            
            // Motel descriptions
            'Basic motel room with convenient parking access.',
            'Motel room with a comfortable king-size bed.',
            'Motel room with a comfortable queen-size bed.',
            'Family-friendly motel room with parking access.',
            'Motel suite with extra space and amenities.',
            
            // Extended Stay descriptions
            'Room designed for long-term stays with extended amenities.',
            'Suite with kitchenette for extended stays.',
            'One bedroom with kitchenette for longer stays.',
            'Two bedrooms with kitchenette for extended stays.',
            'Studio with kitchenette for extended stays.',
            
            // Business Hotel descriptions
            'Room with business amenities and work facilities.',
            'Suite with meeting facilities and business services.',
            'Room with exclusive club lounge access.',
            'Room on executive floor with premium services.',
            
            // Spa Hotel descriptions
            'Room with spa amenities and wellness features.',
            'Suite with comprehensive wellness features.',
            'Villa with private spa facilities and treatments.',
            'Room with dedicated spa treatment area.',
            
            // Eco Lodge descriptions
            'Environmentally friendly room with sustainable features.',
            'Villa with sustainable features and natural materials.',
            'Unique accommodation built among trees.',
            'Lodge built with natural materials and eco-friendly design.',
            
            // Unique Stay descriptions
            'Unique tree house experience with nature immersion.',
            'Room built within a natural cave formation.',
            'Traditional tent accommodation with modern amenities.',
            'Compact capsule accommodation with essential amenities.',
            'Room made entirely of ice and snow.',
            'Accommodation on water with aquatic views.',
            'Luxury camping tent with premium amenities.',
            'Tent in wildlife reserve with safari experience.',
            'Converted train carriage with vintage charm.',
            'Suite in converted airplane with unique design.',
            'Room in converted lighthouse with coastal views.',
            'Room in historic castle with royal ambiance.',
            'Simple room in monastery with spiritual atmosphere.',
            'Traditional ice accommodation with unique experience.'
        ];

        // Features organized by accommodation type and category
        $features = [
            // Hotel features
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Mini Bar'],
            ['WiFi', 'Air Conditioning', 'Business Center', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Spa'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            
            // Apartment features
            ['WiFi', 'Kitchen', 'Air Conditioning', 'TV/Cable'],
            ['WiFi', 'Kitchen', 'Air Conditioning', 'TV/Cable'],
            ['WiFi', 'Kitchen', 'Air Conditioning', 'TV/Cable'],
            ['WiFi', 'Kitchen', 'Air Conditioning', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Spa'],
            ['WiFi', 'Kitchen', 'Air Conditioning', 'Room Service'],
            ['WiFi', 'Kitchen', 'Air Conditioning', 'TV/Cable'],
            ['WiFi', 'Kitchen', 'Air Conditioning', 'TV/Cable'],
            ['WiFi', 'Kitchen', 'Air Conditioning', 'Garden/Lawn'],
            
            // Resort features
            ['WiFi', 'Air Conditioning', 'Swimming Pool', 'Restaurant'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'Swimming Pool', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Swimming Pool', 'Concierge'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Concierge'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Kitchen'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Kitchen'],
            
            // Villa features
            ['WiFi', 'Air Conditioning', 'Kitchen', 'Swimming Pool'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Spa'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Swimming Pool', 'Concierge'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Spa'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'Garden/Lawn'],
            
            // Homestay features
            ['WiFi', 'Kitchen', 'Air Conditioning', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            
            // Heritage features
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Spa'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Balcony/Terrace'],
            
            // Boutique features
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Balcony/Terrace'],
            
            // Hostel features
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            
            // B&B features
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            
            // Guest House features
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            
            // Motel features
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Parking'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Parking'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Parking'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Parking'],
            
            // Extended Stay features
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable'],
            
            // Business Hotel features
            ['WiFi', 'Air Conditioning', 'Business Center', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Business Center', 'Conference Room'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Business Center', 'Concierge'],
            
            // Spa Hotel features
            ['WiFi', 'Air Conditioning', 'Spa', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Spa', 'Concierge'],
            ['WiFi', 'Air Conditioning', 'Spa', 'Concierge'],
            ['WiFi', 'Air Conditioning', 'Spa', 'Room Service'],
            
            // Eco Lodge features
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Safe/Locker'],
            
            // Unique Stay features
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker']
        ];

        // Create accommodations for each property (more comprehensive seeding)
        foreach ($properties as $property) {
            // Get available accommodation types for this property category
            $availableTypes = $accommodationTypes->where('property_category_id', $property->property_category_id);
            
            if ($availableTypes->isEmpty()) {
                continue;
            }
            
            // Create 1-3 accommodations per property
            $numAccommodations = rand(1, 3);
            
            for ($j = 0; $j < $numAccommodations; $j++) {
                $accommodationType = $availableTypes->random();
                
                // Find appropriate name and description based on accommodation type
                $typeName = $accommodationType->name;
                $nameIndex = array_search($typeName, $accommodationNames);
                
                // If exact match not found, use a random name from the same category
                if ($nameIndex === false) {
                    $nameIndex = rand(0, count($accommodationNames) - 1);
                }
                
                // Ensure we don't exceed array bounds
                $descriptionIndex = min($nameIndex, count($descriptions) - 1);
                $featuresIndex = min($nameIndex, count($features) - 1);
                
                // Determine occupancy and pricing based on accommodation type
                $maxOccupancy = $this->getOccupancyForType($typeName);
                $basePrice = $this->getPriceForType($typeName, $property->property_category_id);
                $size = $this->getSizeForType($typeName);
            
            $accommodation = PropertyAccommodation::create([
                'property_id' => $property->id,
                'predefined_accommodation_type_id' => $accommodationType->id,
                    'custom_name' => $accommodationNames[$nameIndex],
                    'max_occupancy' => $maxOccupancy,
                    'base_price' => $basePrice,
                    'size' => $size,
                    'description' => $descriptions[$descriptionIndex],
                    'features' => $features[$featuresIndex],
                    'is_active' => rand(0, 9) < 8, // 80% active
                'uuid' => Str::uuid(),
            ]);

                // Attach appropriate amenities based on accommodation type
                $amenityCount = rand(3, 8);
                $randomAmenities = $amenities->random($amenityCount);
            $accommodation->amenities()->attach($randomAmenities->pluck('id'));
        }
        }
    }
    
    /**
     * Get appropriate occupancy for accommodation type
     */
    private function getOccupancyForType(string $typeName): int
    {
        $occupancyMap = [
            'Single Room' => 1,
            'Studio Apartment' => 2,
            'Standard Room' => 2,
            'Superior Room' => 2,
            'Deluxe Room' => 2,
            'King Room' => 2,
            'Queen Room' => 2,
            'Twin Room' => 2,
            'Double Room' => 2,
            'Executive Room' => 2,
            'Business Suite' => 2,
            'Junior Suite' => 3,
            'Family Room' => 4,
            'Executive Suite' => 4,
            'Presidential Suite' => 4,
            '1 Bedroom Apartment' => 4,
            '2 Bedroom Apartment' => 6,
            '3 Bedroom Apartment' => 8,
            'Entire House' => 6,
            'Entire Villa' => 8,
            'Villa with Pool' => 8,
            'Beach Villa' => 6,
            'Mountain Villa' => 6,
            'Luxury Villa' => 8,
            'Family Villa' => 8,
            'Dormitory' => 8,
            'Female Dorm' => 8,
            'Male Dorm' => 8,
            'Mixed Dorm' => 8,
        ];
        
        return $occupancyMap[$typeName] ?? rand(2, 4);
    }
    
    /**
     * Get appropriate pricing for accommodation type and category
     */
    private function getPriceForType(string $typeName, int $categoryId): float
    {
        // Base pricing by category (in INR)
        $categoryMultipliers = [
            1 => 1.0,    // Hotel
            2 => 1.2,    // Apartment
            3 => 1.5,    // Resort
            4 => 2.0,    // Villa
            5 => 0.8,    // Homestay
            6 => 1.8,    // Heritage
            7 => 1.3,    // Boutique
            8 => 0.3,    // Hostel
            9 => 0.6,    // B&B
            10 => 0.5,   // Guest House
            11 => 0.7,   // Motel
            12 => 1.1,   // Extended Stay
            13 => 1.4,   // Business Hotel
            14 => 1.6,   // Spa Hotel
            15 => 1.0,   // Eco Lodge
            16 => 1.7,   // Unique Stays
        ];
        
        // Type-specific pricing ranges
        $typeRanges = [
            'Standard Room' => [1500, 3500],
            'Superior Room' => [2000, 4500],
            'Deluxe Room' => [2500, 5500],
            'Executive Room' => [3000, 6500],
            'Junior Suite' => [4000, 8000],
            'Executive Suite' => [6000, 12000],
            'Presidential Suite' => [10000, 25000],
            'Family Room' => [2000, 5000],
            'Studio Apartment' => [1800, 4000],
            '1 Bedroom Apartment' => [2500, 5500],
            '2 Bedroom Apartment' => [4000, 8000],
            '3 Bedroom Apartment' => [6000, 12000],
            'Penthouse' => [8000, 20000],
            'Resort Room' => [3000, 7000],
            'Pool Villa' => [8000, 18000],
            'Beach Villa' => [10000, 25000],
            'Overwater Bungalow' => [15000, 35000],
            'Entire Villa' => [12000, 30000],
            'Tree House' => [5000, 12000],
            'Capsule Hotel' => [800, 2000],
            'Dormitory' => [500, 1500],
            'B&B Room' => [1000, 2500],
            'Motel Room' => [800, 2000],
            'Spa Room' => [4000, 9000],
            'Executive Room' => [3500, 7500],
        ];
        
        $range = $typeRanges[$typeName] ?? [1500, 5000];
        $multiplier = $categoryMultipliers[$categoryId] ?? 1.0;
        
        return rand($range[0], $range[1]) * $multiplier;
    }
    
    /**
     * Get appropriate size for accommodation type
     */
    private function getSizeForType(string $typeName): float
    {
        $sizeMap = [
            'Single Room' => 15,
            'Standard Room' => 25,
            'Superior Room' => 30,
            'Deluxe Room' => 35,
            'Executive Room' => 40,
            'Junior Suite' => 50,
            'Executive Suite' => 70,
            'Presidential Suite' => 100,
            'Family Room' => 45,
            'Studio Apartment' => 35,
            '1 Bedroom Apartment' => 55,
            '2 Bedroom Apartment' => 80,
            '3 Bedroom Apartment' => 120,
            'Penthouse' => 200,
            'Resort Room' => 40,
            'Pool Villa' => 150,
            'Beach Villa' => 180,
            'Overwater Bungalow' => 120,
            'Entire Villa' => 200,
            'Tree House' => 30,
            'Capsule Hotel' => 8,
            'Dormitory' => 20,
            'B&B Room' => 20,
            'Motel Room' => 25,
            'Spa Room' => 45,
            'Executive Room' => 45,
        ];
        
        return $sizeMap[$typeName] ?? rand(25, 80);
    }
}
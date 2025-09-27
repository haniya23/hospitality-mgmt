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

        $accommodationNames = [
            'Deluxe Room',
            'Standard Suite',
            'Executive Room',
            'Family Room',
            'Honeymoon Suite',
            'Garden View Room',
            'Poolside Villa',
            'Penthouse Suite',
            'Studio Apartment',
            'Presidential Suite',
            'Ocean View Room',
            'Mountain View Suite',
            'Heritage Room',
            'Business Suite',
            'Luxury Villa',
            'Cozy Cottage',
            'Beachfront Room',
            'Tree House',
            'Royal Suite',
            'Modern Apartment'
        ];

        $descriptions = [
            'Spacious room with modern amenities and comfortable furnishings.',
            'Elegant suite with separate living area and premium amenities.',
            'Business-class room with work desk and high-speed internet.',
            'Perfect for families with extra space and child-friendly amenities.',
            'Romantic suite with special amenities for couples.',
            'Room with beautiful garden views and natural lighting.',
            'Villa with direct access to swimming pool and outdoor facilities.',
            'Top-floor suite with panoramic views and luxury amenities.',
            'Compact apartment with kitchenette and modern facilities.',
            'Ultimate luxury suite with premium services and amenities.',
            'Room with stunning ocean views and beach access.',
            'Suite with breathtaking mountain views and natural surroundings.',
            'Traditional room with heritage charm and modern comfort.',
            'Professional suite with business facilities and meeting space.',
            'Exclusive villa with private facilities and personalized service.',
            'Charming cottage with rustic charm and modern amenities.',
            'Room with direct beach access and oceanfront views.',
            'Unique accommodation with nature-inspired design.',
            'Royal-themed suite with luxury amenities and services.',
            'Contemporary apartment with modern design and facilities.'
        ];

        $features = [
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Mini Bar'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'Business Center', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'Spa', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Balcony/Terrace'],
            ['WiFi', 'Swimming Pool', 'Air Conditioning', 'Garden/Lawn'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Room Service'],
            ['WiFi', 'Kitchen', 'Air Conditioning', 'TV/Cable'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Spa'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Balcony/Terrace'],
            ['WiFi', 'Air Conditioning', 'TV/Cable', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Business Center', 'Conference Room'],
            ['WiFi', 'Swimming Pool', 'Air Conditioning', 'Concierge'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Kitchen'],
            ['WiFi', 'Air Conditioning', 'Balcony/Terrace', 'Room Service'],
            ['WiFi', 'Air Conditioning', 'Garden/Lawn', 'Safe/Locker'],
            ['WiFi', 'Air Conditioning', 'Concierge', 'Spa'],
            ['WiFi', 'Air Conditioning', 'Kitchen', 'TV/Cable']
        ];

        for ($i = 0; $i < 20; $i++) {
            $property = $properties->random();
            $accommodationType = $accommodationTypes->where('property_category_id', $property->property_category_id)->random();
            
            $accommodation = PropertyAccommodation::create([
                'property_id' => $property->id,
                'predefined_accommodation_type_id' => $accommodationType->id,
                'custom_name' => $accommodationNames[$i],
                'max_occupancy' => rand(2, 8),
                'base_price' => rand(1500, 15000), // Random price between 1500-15000
                'description' => $descriptions[$i],
                'features' => $features[$i],
                'is_active' => rand(0, 1) ? true : false,
                'uuid' => Str::uuid(),
            ]);

            // Attach random amenities to the accommodation
            $randomAmenities = $amenities->random(rand(3, 8));
            $accommodation->amenities()->attach($randomAmenities->pluck('id'));
        }
    }
}
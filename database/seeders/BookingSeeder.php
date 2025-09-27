<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\PropertyAccommodation;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Check if bookings already exist
        if (Reservation::count() >= 50) {
            $this->command->info('Bookings already seeded. Skipping...');
            return;
        }

        // Get existing data
        $accommodations = PropertyAccommodation::all();
        $guests = Guest::all();
        $users = User::where('is_admin', false)->get();

        if ($accommodations->isEmpty()) {
            $this->command->warn('No accommodations found. Please run AccommodationSeeder first.');
            return;
        }

        // Create some guests if none exist
        if ($guests->isEmpty()) {
            $guestNames = [
                'Rajesh Kumar', 'Priya Sharma', 'Amit Patel', 'Sneha Singh', 'Vikram Reddy',
                'Anita Gupta', 'Rohit Verma', 'Deepika Joshi', 'Karan Malhotra', 'Sunita Agarwal',
                'Arjun Singh', 'Meera Nair', 'Suresh Iyer', 'Kavya Menon', 'Ravi Krishnan',
                'Shilpa Rao', 'Manoj Kumar', 'Pooja Desai', 'Sandeep Shah', 'Neha Agarwal'
            ];

            foreach ($guestNames as $name) {
                Guest::create([
                    'name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                    'mobile_number' => '9' . rand(100000000, 999999999),
                    'address' => 'Sample Address, City',
                    'id_proof_type' => ['Aadhar', 'Passport', 'Driving License'][rand(0, 2)],
                    'id_proof_number' => 'ID' . rand(100000, 999999),
                ]);
            }
            $guests = Guest::all();
        }

        $statuses = ['confirmed', 'pending', 'cancelled', 'checked_in', 'checked_out'];
        $specialRequests = [
            'Late check-in requested',
            'Early check-out needed',
            'Extra bed required',
            'Vegetarian meals only',
            'Airport pickup needed',
            'Anniversary celebration',
            'Business trip',
            'Family vacation',
            'Honeymoon trip',
            'Group booking',
            null, null, null, null, null // Some bookings without special requests
        ];

        $notes = [
            'Regular customer',
            'VIP guest',
            'First time visitor',
            'Repeat booking',
            'Corporate booking',
            'Group booking',
            'Special occasion',
            'Long stay guest',
            'International guest',
            'Local guest',
            null, null, null, null, null // Some bookings without notes
        ];

        // Create bookings for the next 6 months
        for ($i = 0; $i < 50; $i++) {
            $accommodation = $accommodations->random();
            $guest = $guests->random();
            $user = $users->random();
            
            // Generate random check-in date (within next 6 months)
            $checkInDate = Carbon::now()->addDays(rand(1, 180));
            $checkOutDate = $checkInDate->copy()->addDays(rand(1, 7)); // 1-7 nights stay
            
            $basePrice = $accommodation->base_price;
            $nights = $checkInDate->diffInDays($checkOutDate);
            $totalAmount = $basePrice * $nights;
            
            // Add some variation to pricing
            $totalAmount = $totalAmount * (0.8 + (rand(0, 40) / 100)); // 80% to 120% of base price
            
            $advancePaid = $totalAmount * (0.2 + (rand(0, 60) / 100)); // 20% to 80% advance
            $balancePending = $totalAmount - $advancePaid;
            
            $status = $statuses[rand(0, count($statuses) - 1)];
            
            // Generate confirmation number
            $confirmationNumber = 'BK' . strtoupper(Str::random(6)) . rand(100, 999);
            
            $reservation = Reservation::create([
                'guest_id' => $guest->id,
                'property_accommodation_id' => $accommodation->id,
                'b2b_partner_id' => null, // Set to null to avoid foreign key issues
                'confirmation_number' => $confirmationNumber,
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'adults' => rand(1, 4),
                'children' => rand(0, 3),
                'total_amount' => round($totalAmount, 2),
                'advance_paid' => round($advancePaid, 2),
                'balance_pending' => round($balancePending, 2),
                'rate_override' => rand(0, 1) ? rand(500, 2000) : null,
                'override_reason' => rand(0, 1) ? 'Special discount' : null,
                'status' => $status,
                'special_requests' => $specialRequests[rand(0, count($specialRequests) - 1)],
                'notes' => $notes[rand(0, count($notes) - 1)],
                'created_by' => $user->id,
                'uuid' => Str::uuid(),
            ]);

            // Set timestamps based on status
            if (in_array($status, ['confirmed', 'checked_in', 'checked_out'])) {
                $reservation->confirmed_at = $checkInDate->subDays(rand(1, 30));
                $reservation->save();
            }
            
            if (in_array($status, ['checked_in', 'checked_out'])) {
                $reservation->checked_in_at = $checkInDate->addHours(rand(0, 12));
                $reservation->save();
            }
            
            if ($status === 'checked_out') {
                $reservation->checked_out_at = $checkOutDate->addHours(rand(0, 12));
                $reservation->save();
            }
        }
    }
}
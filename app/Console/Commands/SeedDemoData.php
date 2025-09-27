<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\PropertySeeder;
use Database\Seeders\AccommodationSeeder;
use Database\Seeders\BookingSeeder;

class SeedDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:demo-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed demo data including properties, accommodations, and bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Seeding demo data...');
        
        $this->info('📋 Seeding Properties...');
        $this->call('db:seed', ['--class' => PropertySeeder::class]);
        
        $this->info('🏠 Seeding Accommodations...');
        $this->call('db:seed', ['--class' => AccommodationSeeder::class]);
        
        $this->info('📅 Seeding Bookings...');
        $this->call('db:seed', ['--class' => BookingSeeder::class]);
        
        $this->info('✅ Demo data seeded successfully!');
        
        // Show counts
        $this->info('📊 Data Summary:');
        $this->line('   Properties: ' . \App\Models\Property::count());
        $this->line('   Accommodations: ' . \App\Models\PropertyAccommodation::count());
        $this->line('   Bookings: ' . \App\Models\Reservation::count());
    }
}
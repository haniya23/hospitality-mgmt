<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=admin@hospitality.com} {--password=admin123} {--name=Admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for Filament admin panel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Check if admin user already exists
        $existingAdmin = User::where('email', $email)->first();
        if ($existingAdmin) {
            $this->error("Admin user with email {$email} already exists!");
            return 1;
        }

        // Create admin user
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'is_active' => true,
            'subscription_status' => 'professional',
            'properties_limit' => 999, // Unlimited for admin
            'mobile_number' => '0000000000', // Placeholder
            'pin_hash' => Hash::make('0000'), // Placeholder PIN
            'user_type' => 'admin',
            'is_staff' => false,
        ]);

        $this->info("Admin user created successfully!");
        $this->line("Email: {$email}");
        $this->line("Password: {$password}");
        $this->line("Name: {$name}");
        $this->line("");
        $this->warn("Please change the password after first login!");
        $this->line("Admin panel URL: " . url('/admin'));

        return 0;
    }
}
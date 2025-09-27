<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    protected $signature = 'user:create-test {--email=test@example.com} {--password=password}';
    protected $description = 'Create a test user for development';

    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->info("User with email {$email} already exists.");
            return;
        }

        $user = User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => Hash::make($password),
            'mobile_number' => '987654321' . rand(1, 9),
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->info("Test user created successfully!");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->info("User ID: {$user->id}");
    }
}
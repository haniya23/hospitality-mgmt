<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TrialFixSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure all trial users are set to professional trial so advanced features show
        User::where('subscription_status', 'trial')
            ->update(['trial_plan' => 'professional']);
    }
}




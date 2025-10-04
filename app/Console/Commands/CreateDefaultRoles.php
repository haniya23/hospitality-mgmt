<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use App\Models\Role;

class CreateDefaultRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:create-defaults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default roles (Manager and Supervisor) for all existing properties';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating default roles for all properties...');
        
        $properties = Property::all();
        $createdCount = 0;
        
        foreach ($properties as $property) {
            // Check if property already has roles
            $existingRoles = Role::where('property_id', $property->id)->count();
            
            if ($existingRoles === 0) {
                Role::createDefaultRoles($property->id);
                $this->line("Created default roles for property: {$property->name}");
                $createdCount++;
            } else {
                $this->line("Property '{$property->name}' already has {$existingRoles} roles, skipping...");
            }
        }
        
        $this->info("Completed! Created default roles for {$createdCount} properties.");
        
        return Command::SUCCESS;
    }
}
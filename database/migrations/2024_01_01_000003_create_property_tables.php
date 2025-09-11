<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'pending', 'active', 'inactive', 'suspended'])->default('draft');
            $table->timestamps();
        });

        Schema::create('property_locations', function (Blueprint $table) {
            $table->foreignId('property_id')->primary()->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->nullable()->constrained();
            $table->foreignId('district_id')->nullable()->constrained();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->foreignId('pincode_id')->nullable()->constrained();
            $table->text('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_locations');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('property_categories');
    }
};
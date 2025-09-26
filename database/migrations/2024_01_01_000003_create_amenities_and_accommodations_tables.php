<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Amenities table
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        // Predefined accommodation types
        Schema::create('predefined_accommodation_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Property accommodations
        Schema::create('property_accommodations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('predefined_accommodation_type_id')->constrained()->cascadeOnDelete();
            $table->string('custom_name')->nullable();
            $table->integer('max_occupancy')->default(2);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Property amenities pivot table
        Schema::create('property_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Accommodation amenities pivot table
        Schema::create('accommodation_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained('property_accommodations')->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['accommodation_id', 'amenity_id']);
        });

        // Property policies
        Schema::create('property_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('house_rules')->nullable();
            $table->timestamps();
        });

        // Property photos
        Schema::create('property_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->nullable()->constrained('property_accommodations')->onDelete('cascade');
            $table->string('file_path');
            $table->string('caption')->default('general');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_main')->default(false);
            $table->unsignedInteger('file_size')->comment('File size in bytes, max 512KB');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_photos');
        Schema::dropIfExists('property_policies');
        Schema::dropIfExists('accommodation_amenities');
        Schema::dropIfExists('property_amenity');
        Schema::dropIfExists('property_accommodations');
        Schema::dropIfExists('predefined_accommodation_types');
        Schema::dropIfExists('amenities');
    }
};

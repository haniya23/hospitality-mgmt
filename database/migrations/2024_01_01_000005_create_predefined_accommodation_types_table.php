<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predefined_accommodation_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('property_accommodations', function (Blueprint $table) {
            $table->id();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('property_accommodations');
        Schema::dropIfExists('predefined_accommodation_types');
    }
};
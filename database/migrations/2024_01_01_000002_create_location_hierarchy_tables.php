<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 3)->unique();
            $table->timestamps();
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->timestamps();
            $table->unique(['country_id', 'name']);
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['state_id', 'name']);
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['district_id', 'name']);
        });

        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10);
            $table->timestamps();
            $table->unique(['city_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincodes');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b2b_partners', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('partner_name')->unique();
            $table->string('partner_type'); // OTA, Corporate, Travel Agent
            $table->foreignId('contact_user_id')->nullable()->constrained('users');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });

        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('id_type')->default('aadhar'); // passport, license, etc.
            $table->string('id_number');
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_accommodation_id')->constrained('property_accommodations')->cascadeOnDelete();
            $table->foreignId('b2b_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->string('confirmation_number')->unique();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('confirmed');
            $table->text('special_requests')->nullable();
            $table->timestamps();
        });

        // Removed reservation_rooms table as we're using property_accommodations directly
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('guests');
        Schema::dropIfExists('b2b_partners');
    }
};
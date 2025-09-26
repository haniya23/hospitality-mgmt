<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // B2B Partners table
        Schema::create('b2b_partners', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('partner_name')->unique();
            $table->string('partner_type'); // OTA, Corporate, Travel Agent
            $table->foreignId('contact_user_id')->nullable()->constrained('users');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('default_discount_pct', 5, 2)->default(0);
            $table->json('partnership_settings')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users');
            $table->timestamp('partnership_accepted_at')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // Guests table
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('mobile_number')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('id_type')->default('aadhar'); // passport, license, etc.
            $table->string('id_number');
            $table->integer('loyalty_points')->default(0);
            $table->integer('total_stays')->default(0);
            $table->timestamp('last_stay_at')->nullable();
            $table->foreignId('partner_id')->nullable()->constrained('b2b_partners')->nullOnDelete();
            $table->boolean('is_reserved')->default(false);
            $table->timestamps();
            
            $table->index('mobile_number');
            $table->index(['is_reserved', 'partner_id']);
        });

        // Reservations table
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
            $table->enum('booking_type', ['per_day', 'per_person'])->default('per_day');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('advance_paid', 10, 2)->default(0);
            $table->decimal('balance_pending', 10, 2)->default(0);
            $table->decimal('rate_override', 10, 2)->nullable();
            $table->string('override_reason')->nullable();
            $table->enum('status', ['pending', 'active', 'confirmed', 'checked_in', 'checked_out', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();
        });

        // Cancelled bookings table
        Schema::create('cancelled_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->foreignId('cancelled_by')->constrained('users');
            $table->timestamp('cancelled_at');
            $table->timestamps();
        });

        // B2B Requests table
        Schema::create('b2b_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('from_partner_id')->constrained('users');
            $table->foreignId('to_property_id')->constrained('properties');
            $table->foreignId('guest_id')->nullable()->constrained('guests');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->decimal('quoted_price', 10, 2);
            $table->decimal('counter_price', 10, 2)->nullable();
            $table->enum('status', ['pending', 'countered', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->text('initial_notes')->nullable();
            $table->json('negotiation_history')->nullable();
            $table->foreignId('converted_booking_id')->nullable()->constrained('reservations');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['to_property_id', 'status']);
            $table->index(['from_partner_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b2b_requests');
        Schema::dropIfExists('cancelled_bookings');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('guests');
        Schema::dropIfExists('b2b_partners');
    }
};

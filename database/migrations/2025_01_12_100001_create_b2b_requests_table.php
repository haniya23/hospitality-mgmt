<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }
};
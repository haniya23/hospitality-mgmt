<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_finances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('finance_number', 20)->unique();

            // Relationships
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->constrained('property_accommodations')->cascadeOnDelete();
            $table->foreignId('b2b_partner_id')->nullable()->constrained()->nullOnDelete();

            // Booking details
            $table->date('booking_date');
            $table->date('check_in_date');
            $table->date('check_out_date');

            // Financial tracking
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('advance_received', 12, 2)->default(0);
            $table->decimal('balance_pending', 12, 2)->default(0);
            $table->decimal('additional_charges', 12, 2)->default(0);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);

            // Status tracking
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->enum('booking_status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'completed'])->default('pending');

            // Metadata
            $table->timestamp('last_payment_date')->nullable();
            $table->text('notes')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes for common queries
            $table->index(['property_id', 'payment_status']);
            $table->index(['property_id', 'booking_status']);
            $table->index(['property_id', 'booking_date']);
            $table->index(['payment_status', 'balance_pending']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_finances');
    }
};

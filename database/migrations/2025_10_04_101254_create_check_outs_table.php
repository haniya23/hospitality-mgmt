<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('check_in_id')->nullable()->constrained('check_ins')->onDelete('set null');
            
            // Guest & Room Details
            $table->string('guest_name');
            $table->string('room_number')->nullable();
            
            // Stay Review
            $table->datetime('check_out_time');
            $table->json('services_used')->nullable(); // restaurant, spa, minibar, transport, etc.
            $table->decimal('late_checkout_charges', 10, 2)->default(0);
            $table->text('service_notes')->nullable();
            
            // Final Settlement
            $table->decimal('final_bill', 10, 2);
            $table->decimal('deposit_refund', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'completed', 'partial', 'refunded'])->default('pending');
            $table->text('payment_notes')->nullable();
            
            // Feedback
            $table->integer('rating')->nullable(); // 1-5 stars
            $table->text('feedback_comments')->nullable();
            
            // Signatures
            $table->text('guest_signature')->nullable();
            $table->text('staff_signature')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->boolean('room_marked_clean')->default(false);
            
            $table->timestamps();
            
            $table->index(['reservation_id', 'status']);
            $table->index(['guest_id', 'check_out_time']);
            $table->index(['check_in_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_outs');
    }
};

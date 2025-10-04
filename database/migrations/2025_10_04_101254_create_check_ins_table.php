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
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            
            // Guest Information
            $table->string('guest_name');
            $table->string('guest_contact');
            $table->string('guest_email')->nullable();
            $table->text('guest_address')->nullable();
            $table->string('id_proof_type')->nullable(); // passport, aadhaar, driving_license
            $table->string('id_proof_number')->nullable();
            $table->string('nationality')->nullable();
            
            // Check-in Details
            $table->datetime('check_in_time');
            $table->date('expected_check_out_date');
            $table->text('special_requests')->nullable();
            $table->text('notes')->nullable();
            
            // Signatures
            $table->text('guest_signature')->nullable();
            $table->text('staff_signature')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            
            $table->timestamps();
            
            $table->index(['reservation_id', 'status']);
            $table->index(['guest_id', 'check_in_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};

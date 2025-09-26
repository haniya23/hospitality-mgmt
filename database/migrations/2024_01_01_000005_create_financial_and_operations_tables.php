<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Financial tables
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('reservation_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->date('due_date');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'card', 'bank_transfer', 'upi', 'wallet'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('gateway')->nullable(); // razorpay, stripe, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('type', ['payment', 'refund', 'chargeback']);
            $table->json('gateway_response')->nullable();
            $table->timestamps();
        });

        // Operations tables
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_accommodation_id')->nullable()->constrained('property_accommodations')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->enum('task_type', ['cleaning', 'maintenance', 'inspection', 'setup']);
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_accommodation_id')->nullable()->constrained('property_accommodations')->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['electrical', 'plumbing', 'hvac', 'furniture', 'appliance', 'other']);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('guest_stays', function (Blueprint $table) {
            $table->foreignId('reservation_id')->primary()->constrained()->cascadeOnDelete();
            $table->timestamp('actual_check_in')->nullable();
            $table->timestamp('actual_check_out')->nullable();
            $table->integer('actual_adults')->nullable();
            $table->integer('actual_children')->nullable();
            $table->text('check_in_notes')->nullable();
            $table->text('check_out_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('guest_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('overall_rating')->nullable(); // 1-5
            $table->integer('cleanliness_rating')->nullable();
            $table->integer('service_rating')->nullable();
            $table->integer('amenities_rating')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('would_recommend')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_feedback');
        Schema::dropIfExists('guest_stays');
        Schema::dropIfExists('maintenance_tickets');
        Schema::dropIfExists('housekeeping_tasks');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};

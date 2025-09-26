<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // RBAC tables
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('module')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['property_id', 'name']);
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'property_id', 'role_id']);
        });

        // Pricing rules
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->nullable()->constrained('property_accommodations')->cascadeOnDelete();
            $table->string('rule_name');
            $table->enum('rule_type', ['seasonal', 'promotional', 'b2b_contract', 'loyalty_discount']);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rate_adjustment', 10, 2)->nullable(); // Fixed amount
            $table->decimal('percentage_adjustment', 5, 2)->nullable(); // Percentage
            $table->integer('min_stay_nights')->nullable();
            $table->integer('max_stay_nights')->nullable();
            $table->json('applicable_days')->nullable(); // [1,2,3,4,5,6,7] for days of week
            $table->foreignId('b2b_partner_id')->nullable()->constrained();
            $table->string('promo_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher number = higher priority
            $table->timestamps();

            $table->index(['property_id', 'is_active']);
            $table->index(['property_id', 'accommodation_id', 'is_active']);
            $table->index(['start_date', 'end_date']);
            $table->index(['rule_type', 'is_active']);
        });

        // Commissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('b2b_partners');
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'disputed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users');
            $table->text('payment_notes')->nullable();
            $table->timestamps();

            $table->index(['partner_id', 'status']);
            $table->index(['booking_id']);
        });

        // Audit logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // created, updated, deleted, status_changed, etc.
            $table->string('model_type'); // App\Models\Reservation, etc.
            $table->unsignedBigInteger('model_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });

        // Subscription requests
        Schema::create('subscription_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('requested_plan', ['starter', 'professional']);
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // Referral program
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_id', 12)->unique()->nullable();
            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('referred_id');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->decimal('reward_amount', 8, 2)->default(199.00);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('referrer_id')->references('id')->on('users');
            $table->foreign('referred_id')->references('id')->on('users');
        });

        Schema::create('referral_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_withdrawals');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('subscription_requests');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('pricing_rules');
        Schema::dropIfExists('staff_assignments');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};

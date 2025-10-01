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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_slug'); // trial, starter, professional
            $table->string('plan_name');
            $table->enum('status', ['trial', 'active', 'pending', 'expired', 'cancelled'])->default('trial');
            $table->integer('base_accommodation_limit')->default(3);
            $table->integer('addon_count')->default(0);
            $table->timestamp('start_at');
            $table->timestamp('current_period_end');
            $table->enum('billing_interval', ['month', 'year'])->default('month');
            $table->integer('price_cents')->default(0);
            $table->string('currency', 3)->default('INR');
            $table->string('cashfree_order_id')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'current_period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

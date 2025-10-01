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
        Schema::table('payments', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('payments', 'subscription_id')) {
                $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('payments', 'cashfree_order_id')) {
                $table->string('cashfree_order_id')->nullable();
            }
            if (!Schema::hasColumn('payments', 'payment_id')) {
                $table->string('payment_id')->nullable();
            }
            if (!Schema::hasColumn('payments', 'amount_cents')) {
                $table->integer('amount_cents')->nullable();
            }
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 3)->default('INR');
            }
            if (!Schema::hasColumn('payments', 'raw_response')) {
                $table->json('raw_response')->nullable();
            }
            
            $table->index(['subscription_id', 'status']);
            $table->index(['cashfree_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn([
                'subscription_id',
                'cashfree_order_id',
                'payment_id',
                'amount_cents',
                'currency',
                'method',
                'raw_response'
            ]);
        });
    }
};

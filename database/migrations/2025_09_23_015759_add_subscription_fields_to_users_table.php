<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('subscription_status', ['trial', 'active', 'expired', 'cancelled'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->integer('properties_limit')->default(3);
            $table->boolean('is_trial_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subscription_status', 'trial_ends_at', 'subscription_ends_at', 'properties_limit', 'is_trial_active']);
        });
    }
};
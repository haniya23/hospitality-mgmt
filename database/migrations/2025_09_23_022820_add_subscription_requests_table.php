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
        Schema::create('subscription_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('requested_plan', ['starter', 'professional']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('trial_plan', ['starter', 'professional'])->default('starter')->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_requests');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('trial_plan');
        });
    }
};

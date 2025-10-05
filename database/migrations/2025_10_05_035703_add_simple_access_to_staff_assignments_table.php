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
        Schema::table('staff_assignments', function (Blueprint $table) {
            $table->boolean('booking_access')->default(false)->after('status');
            $table->boolean('guest_service_access')->default(false)->after('booking_access');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_assignments', function (Blueprint $table) {
            $table->dropColumn(['booking_access', 'guest_service_access']);
        });
    }
};

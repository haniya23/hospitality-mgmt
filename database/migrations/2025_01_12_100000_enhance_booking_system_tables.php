<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enhance existing reservations table to support full booking workflow
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'confirmed', 'checked_in', 'checked_out', 'completed', 'cancelled', 'no_show'])->default('pending')->change();
            $table->decimal('advance_paid', 10, 2)->default(0)->after('total_amount');
            $table->decimal('balance_pending', 10, 2)->default(0)->after('advance_paid');
            $table->decimal('rate_override', 10, 2)->nullable()->after('balance_pending');
            $table->string('override_reason')->nullable()->after('rate_override');
            $table->foreignId('created_by')->nullable()->constrained('users')->after('special_requests');
            $table->text('notes')->nullable()->after('created_by');
            $table->timestamp('confirmed_at')->nullable()->after('notes');
            $table->timestamp('checked_in_at')->nullable()->after('confirmed_at');
            $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
        });

        // Enhance guests table to support customer loyalty
        Schema::table('guests', function (Blueprint $table) {
            $table->string('mobile_number')->nullable()->after('phone');
            $table->integer('loyalty_points')->default(0)->after('id_number');
            $table->integer('total_stays')->default(0)->after('loyalty_points');
            $table->timestamp('last_stay_at')->nullable()->after('total_stays');
            $table->index('mobile_number');
        });

        // Enhance b2b_partners table for partnership management
        Schema::table('b2b_partners', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended', 'rejected'])->default('pending')->change();
            $table->decimal('default_discount_pct', 5, 2)->default(0)->after('commission_rate');
            $table->json('partnership_settings')->nullable()->after('default_discount_pct');
            $table->foreignId('requested_by')->nullable()->constrained('users')->after('partnership_settings');
            $table->timestamp('partnership_accepted_at')->nullable()->after('requested_by');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'advance_paid', 'balance_pending', 'rate_override', 'override_reason',
                'created_by', 'notes', 'confirmed_at', 'checked_in_at', 'checked_out_at'
            ]);
            $table->enum('status', ['confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('confirmed')->change();
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropIndex(['mobile_number']);
            $table->dropColumn(['mobile_number', 'loyalty_points', 'total_stays', 'last_stay_at']);
        });

        Schema::table('b2b_partners', function (Blueprint $table) {
            $table->dropColumn(['default_discount_pct', 'partnership_settings', 'requested_by', 'partnership_accepted_at']);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->change();
        });
    }
};
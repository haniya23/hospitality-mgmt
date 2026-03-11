<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('income_records', function (Blueprint $table) {
            // Add payment_id to link income records to payments
            $table->foreignId('payment_id')->nullable()->after('reservation_id')->constrained()->nullOnDelete();

            // Add index for faster lookups
            $table->index('payment_id');
        });

        // Update income_type enum to include b2b_booking
        // Note: In MySQL, we need to alter the column type
        DB::statement("ALTER TABLE income_records MODIFY COLUMN income_type ENUM('booking', 'rental', 'service', 'deposit', 'penalty', 'commission', 'b2b_booking', 'other') DEFAULT 'booking'");
    }

    public function down(): void
    {
        Schema::table('income_records', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
        });

        // Revert enum
        DB::statement("ALTER TABLE income_records MODIFY COLUMN income_type ENUM('booking', 'rental', 'service', 'deposit', 'penalty', 'commission', 'other') DEFAULT 'booking'");
    }
};

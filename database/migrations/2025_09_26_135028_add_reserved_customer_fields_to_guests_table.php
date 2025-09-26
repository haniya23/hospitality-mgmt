<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->foreignId('partner_id')->nullable()->constrained('b2b_partners')->nullOnDelete();
            $table->boolean('is_reserved')->default(false);
            $table->index(['is_reserved', 'partner_id']);
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropIndex(['is_reserved', 'partner_id']);
            $table->dropColumn(['partner_id', 'is_reserved']);
        });
    }
};
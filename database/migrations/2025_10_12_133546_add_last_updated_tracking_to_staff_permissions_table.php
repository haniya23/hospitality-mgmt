<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_permissions', function (Blueprint $table) {
            $table->foreignId('last_updated_by')->nullable()->after('can_manage_permissions')->constrained('users')->nullOnDelete();
            $table->timestamp('last_updated_at')->nullable()->after('last_updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('staff_permissions', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn(['last_updated_by', 'last_updated_at']);
        });
    }
};
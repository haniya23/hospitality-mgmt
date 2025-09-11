<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('is_active');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->integer('wizard_step_completed')->default(0)->after('status');
            $table->timestamp('approved_at')->nullable()->after('wizard_step_completed');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['wizard_step_completed', 'approved_at', 'approved_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
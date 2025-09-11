<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('rule_name');
            $table->enum('rule_type', ['seasonal', 'promotional', 'b2b_contract', 'loyalty_discount']);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rate_adjustment', 10, 2)->nullable(); // Fixed amount
            $table->decimal('percentage_adjustment', 5, 2)->nullable(); // Percentage
            $table->integer('min_stay_nights')->nullable();
            $table->integer('max_stay_nights')->nullable();
            $table->json('applicable_days')->nullable(); // [1,2,3,4,5,6,7] for days of week
            $table->foreignId('b2b_partner_id')->nullable()->constrained();
            $table->string('promo_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher number = higher priority
            $table->timestamps();

            $table->index(['property_id', 'is_active']);
            $table->index(['start_date', 'end_date']);
            $table->index(['rule_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
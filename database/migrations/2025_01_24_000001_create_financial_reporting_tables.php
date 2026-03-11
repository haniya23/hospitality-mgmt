<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Expense Categories (Master Table)
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->default('#6B7280');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Income Records
        Schema::create('income_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->nullable()->constrained('property_accommodations')->nullOnDelete();
            $table->foreignId('b2b_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('income_type', ['booking', 'rental', 'service', 'deposit', 'penalty', 'commission', 'other'])->default('booking');
            $table->decimal('amount', 12, 2);
            $table->date('transaction_date');
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'transaction_date']);
            $table->index(['income_type', 'payment_status']);
        });

        // Expense Records
        Schema::create('expense_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->nullable()->constrained('property_accommodations')->nullOnDelete();
            $table->foreignId('expense_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->date('transaction_date');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'upi', 'cheque', 'other'])->default('cash');
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid');
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('receipt_number')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'transaction_date']);
            $table->index(['expense_category_id', 'payment_status']);
        });

        // Financial Adjustments (for audit trail)
        Schema::create('financial_adjustments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('adjustable'); // Links to income_records or expense_records
            $table->enum('adjustment_type', ['credit', 'debit']);
            $table->decimal('original_amount', 12, 2);
            $table->decimal('adjusted_amount', 12, 2);
            $table->decimal('adjustment_difference', 12, 2);
            $table->text('reason');
            $table->foreignId('adjusted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Financial Periods (for locking)
        Schema::create('financial_periods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('period_type', ['weekly', 'monthly']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'locked', 'closed'])->default('open');
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'period_type', 'start_date']);
            $table->index(['status', 'period_type']);
        });

        // Financial Reports (snapshots)
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('financial_period_id')->constrained()->cascadeOnDelete();
            $table->enum('report_type', ['weekly', 'monthly', 'custom']);
            $table->string('report_number')->unique();
            $table->decimal('total_income', 14, 2)->default(0);
            $table->decimal('total_expenses', 14, 2)->default(0);
            $table->decimal('net_profit', 14, 2)->default(0);
            $table->decimal('outstanding_receivables', 14, 2)->default(0);
            $table->decimal('outstanding_payables', 14, 2)->default(0);
            $table->json('summary_data')->nullable();
            $table->enum('status', ['draft', 'approved', 'locked'])->default('draft');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'report_type', 'status']);
        });

        // Financial Report Items (detailed breakdown)
        Schema::create('financial_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_report_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['income', 'expense']);
            $table->string('category');
            $table->decimal('amount', 14, 2)->default(0);
            $table->integer('transaction_count')->default(0);
            $table->json('breakdown')->nullable();
            $table->timestamps();

            $table->index(['financial_report_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_report_items');
        Schema::dropIfExists('financial_reports');
        Schema::dropIfExists('financial_periods');
        Schema::dropIfExists('financial_adjustments');
        Schema::dropIfExists('expense_records');
        Schema::dropIfExists('income_records');
        Schema::dropIfExists('expense_categories');
    }
};

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
        // Drop dependent tables first (in correct order)
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('checklist_executions');
        Schema::dropIfExists('staff_notifications');
        Schema::dropIfExists('staff_tasks');
        Schema::dropIfExists('cleaning_checklists');
        Schema::dropIfExists('staff_permissions');
        Schema::dropIfExists('staff_activity_logs');
        Schema::dropIfExists('staff_assignments');
        
        // Create fresh staff_assignments table
        Schema::create('staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->boolean('booking_access')->default(false);
            $table->boolean('guest_service_access')->default(false);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'property_id']);
            $table->index(['property_id', 'status']);
        });

        // Create staff_tasks table
        Schema::create('staff_tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->enum('task_type', ['cleaning', 'maintenance', 'guest_service', 'check_in', 'check_out', 'inspection', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->text('completion_notes')->nullable();
            $table->timestamps();
            
            $table->index(['staff_assignment_id', 'status']);
            $table->index(['property_id', 'scheduled_at']);
        });

        // Create staff_notifications table
        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['task_assignment', 'urgent_update', 'reminder', 'general'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('action_data')->nullable();
            $table->timestamps();
            
            $table->index(['staff_assignment_id', 'is_read']);
            $table->index(['from_user_id', 'created_at']);
        });

        // Create cleaning_checklists table
        Schema::create('cleaning_checklists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create checklist_executions table
        Schema::create('checklist_executions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('cleaning_checklist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_accommodation_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'skipped'])->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['staff_assignment_id', 'status']);
            $table->index(['property_accommodation_id', 'created_at']);
        });

        // Create attendance table
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'half_day'])->default('present');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['staff_assignment_id', 'date']);
        });

        // Create leave_requests table
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('leave_type', ['sick', 'vacation', 'personal', 'emergency', 'other'])->default('personal');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->index(['staff_assignment_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_executions');
        Schema::dropIfExists('cleaning_checklists');
        Schema::dropIfExists('staff_notifications');
        Schema::dropIfExists('staff_tasks');
        Schema::dropIfExists('staff_assignments');
    }
};
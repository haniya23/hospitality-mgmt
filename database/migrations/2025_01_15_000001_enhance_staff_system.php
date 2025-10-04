<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add user_type to users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['owner', 'staff', 'admin'])->default('owner')->after('is_admin');
            $table->boolean('is_staff')->default(false)->after('user_type');
        });

        // Create staff_tasks table for daily task management
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
            $table->datetime('scheduled_at')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->json('completion_photos')->nullable(); // Array of photo URLs
            $table->foreignId('assigned_by')->constrained('users'); // Owner who assigned the task
            $table->timestamps();

            $table->index(['staff_assignment_id', 'status']);
            $table->index(['property_id', 'status']);
            $table->index(['scheduled_at']);
            $table->index(['task_type', 'status']);
        });

        // Create cleaning_checklists table for reusable checklists
        Schema::create('cleaning_checklists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Standard Room Cleaning", "Deep Clean", "Check-out Cleaning"
            $table->text('description')->nullable();
            $table->json('checklist_items'); // Array of checklist items
            $table->boolean('is_active')->default(true);
            $table->boolean('is_template')->default(false); // Can be reused across properties
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['property_id', 'is_active']);
        });

        // Create checklist_executions table to track checklist completions
        Schema::create('checklist_executions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('cleaning_checklist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_accommodation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->cascadeOnDelete(); // If related to a booking
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->json('completed_items')->nullable(); // Track which items are completed
            $table->text('notes')->nullable();
            $table->json('photos')->nullable(); // Photos of completed work
            $table->datetime('started_at');
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['cleaning_checklist_id', 'status']);
            $table->index(['staff_assignment_id', 'status']);
        });

        // Create staff_notifications table for owner-staff communication
        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_user_id')->constrained('users'); // Owner who sent the notification
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['task_assignment', 'urgent_update', 'reminder', 'general'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_read')->default(false);
            $table->datetime('read_at')->nullable();
            $table->json('action_data')->nullable(); // Additional data for action buttons
            $table->timestamps();

            $table->index(['staff_assignment_id', 'is_read']);
            $table->index(['type', 'priority']);
        });

        // Create staff_permissions table for granular permission control
        Schema::create('staff_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('permission_key'); // e.g., 'view_bookings', 'update_task_status', 'upload_photos'
            $table->boolean('is_granted')->default(true);
            $table->json('restrictions')->nullable(); // Additional restrictions (e.g., only specific accommodations)
            $table->timestamps();

            $table->unique(['staff_assignment_id', 'permission_key']);
        });

        // Create staff_activity_logs table for detailed activity tracking
        Schema::create('staff_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // e.g., 'task_started', 'task_completed', 'photo_uploaded', 'checklist_completed'
            $table->string('model_type')->nullable(); // App\Models\StaffTask, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('data')->nullable(); // Additional data about the action
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['staff_assignment_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_activity_logs');
        Schema::dropIfExists('staff_permissions');
        Schema::dropIfExists('staff_notifications');
        Schema::dropIfExists('checklist_executions');
        Schema::dropIfExists('cleaning_checklists');
        Schema::dropIfExists('staff_tasks');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'is_staff']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * New Multi-Level Staff Hierarchy System:
     * Owner → Manager → Supervisor → Staff
     * 
     * Features:
     * - Department-based staff categorization
     * - Task assignment with proof uploads
     * - Verification workflows
     * - Activity logging
     */
    public function up(): void
    {
        // Departments Table (e.g., Housekeeping, Maintenance, F&B, Front Office, etc.)
        Schema::create('staff_departments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name'); // e.g., "Housekeeping", "Maintenance"
            $table->string('code')->unique(); // e.g., "HOUSEKEEPING", "MAINTENANCE"
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->string('color')->default('#3B82F6'); // Badge color
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Staff Members Table
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('staff_departments')->nullOnDelete();
            $table->enum('staff_role', ['manager', 'supervisor', 'staff']); // Hierarchy role
            $table->string('job_title')->nullable(); // e.g., "Senior Housekeeper", "Electrician"
            $table->foreignId('reports_to')->nullable()->constrained('staff_members')->nullOnDelete(); // Hierarchical relationship
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'temporary'])->default('full_time');
            $table->enum('status', ['active', 'inactive', 'on_leave', 'suspended'])->default('active');
            $table->date('join_date');
            $table->date('end_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['property_id', 'staff_role', 'status']);
            $table->index(['reports_to', 'status']);
            $table->index(['department_id', 'status']);
        });

        // Tasks Table (replacing old staff_tasks)
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('staff_departments')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('task_type', [
                'cleaning', 
                'maintenance', 
                'guest_service', 
                'inspection', 
                'delivery',
                'setup',
                'inventory',
                'administrative',
                'other'
            ])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'verified', 'rejected', 'cancelled'])->default('pending');
            
            // Assignment tracking
            $table->foreignId('created_by')->constrained('users'); // Owner/Manager who created
            $table->foreignId('assigned_to')->nullable()->constrained('staff_members')->nullOnDelete(); // Staff member assigned
            $table->foreignId('assigned_by')->nullable()->constrained('staff_members')->nullOnDelete(); // Supervisor who assigned
            
            // Scheduling
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            
            // Completion & Verification
            $table->text('completion_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('staff_members')->nullOnDelete();
            $table->text('verification_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Metadata
            $table->string('location')->nullable(); // Room number, area, etc.
            $table->json('checklist_items')->nullable(); // Optional checklist
            $table->boolean('requires_photo_proof')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['property_id', 'status', 'scheduled_at']);
            $table->index(['assigned_to', 'status']);
            $table->index(['department_id', 'status']);
            $table->index(['due_at', 'status']);
        });

        // Task Media Table (for proof uploads - photos, documents)
        Schema::create('task_media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->default('image'); // image, document, video
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->nullable(); // bytes
            $table->enum('media_type', ['proof', 'before', 'after', 'issue', 'other'])->default('proof');
            $table->text('caption')->nullable();
            $table->timestamps();
            
            $table->index(['task_id', 'media_type']);
        });

        // Task Activity Log Table
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('staff_member_id')->nullable()->constrained('staff_members')->nullOnDelete();
            $table->enum('action', [
                'created',
                'assigned',
                'started',
                'paused',
                'resumed',
                'completed',
                'verified',
                'rejected',
                'reassigned',
                'cancelled',
                'commented',
                'updated'
            ]);
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional context (e.g., assigned_from, assigned_to)
            $table->timestamp('performed_at');
            $table->timestamps();
            
            $table->index(['task_id', 'performed_at']);
            $table->index(['action', 'performed_at']);
        });

        // Staff Notifications Table (for the hierarchy communication)
        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete(); // Recipient
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete(); // Sender
            $table->foreignId('task_id')->nullable()->constrained('tasks')->cascadeOnDelete(); // Related task
            $table->string('type'); // task_assigned, task_completed, task_overdue, etc.
            $table->string('title');
            $table->text('message');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('action_data')->nullable(); // Link, button data, etc.
            $table->timestamps();
            
            $table->index(['staff_member_id', 'is_read']);
            $table->index(['type', 'created_at']);
        });

        // Staff Attendance Table
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave', 'holiday'])->default('present');
            $table->text('notes')->nullable();
            $table->json('location_data')->nullable(); // GPS coordinates for check-in/out
            $table->timestamps();
            
            $table->unique(['staff_member_id', 'date']);
            $table->index(['date', 'status']);
        });

        // Leave Requests Table
        Schema::create('staff_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->default(0);
            $table->enum('leave_type', ['sick', 'vacation', 'personal', 'emergency', 'maternity', 'paternity', 'other'])->default('personal');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->json('attachments')->nullable(); // Medical certificates, etc.
            $table->timestamps();
            
            $table->index(['staff_member_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });

        // Staff Performance Reviews (Optional - for future enhancement)
        Schema::create('staff_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->constrained('users');
            $table->date('review_period_start');
            $table->date('review_period_end');
            $table->integer('task_completion_rate')->nullable(); // Percentage
            $table->integer('average_task_rating')->nullable(); // 1-5 scale
            $table->integer('punctuality_score')->nullable(); // Based on attendance
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals')->nullable();
            $table->enum('overall_rating', ['needs_improvement', 'meets_expectations', 'exceeds_expectations', 'outstanding'])->nullable();
            $table->timestamps();
            
            $table->index(['staff_member_id', 'review_period_end'], 'staff_performance_review_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_performance_reviews');
        Schema::dropIfExists('staff_leave_requests');
        Schema::dropIfExists('staff_attendance');
        Schema::dropIfExists('staff_notifications');
        Schema::dropIfExists('task_logs');
        Schema::dropIfExists('task_media');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('staff_members');
        Schema::dropIfExists('staff_departments');
    }
};

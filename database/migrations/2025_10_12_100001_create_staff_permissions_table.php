<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_id')->constrained()->onDelete('cascade');
            
            // Module Access
            $table->boolean('can_view_reservations')->default(false);
            $table->boolean('can_create_reservations')->default(false);
            $table->boolean('can_edit_reservations')->default(false);
            $table->boolean('can_delete_reservations')->default(false);
            
            $table->boolean('can_view_guests')->default(false);
            $table->boolean('can_create_guests')->default(false);
            $table->boolean('can_edit_guests')->default(false);
            $table->boolean('can_delete_guests')->default(false);
            
            $table->boolean('can_view_properties')->default(false);
            $table->boolean('can_edit_properties')->default(false);
            
            $table->boolean('can_view_accommodations')->default(false);
            $table->boolean('can_edit_accommodations')->default(false);
            
            $table->boolean('can_view_payments')->default(false);
            $table->boolean('can_create_payments')->default(false);
            $table->boolean('can_edit_payments')->default(false);
            
            $table->boolean('can_view_invoices')->default(false);
            $table->boolean('can_create_invoices')->default(false);
            
            $table->boolean('can_view_tasks')->default(false);
            $table->boolean('can_create_tasks')->default(false);
            $table->boolean('can_edit_tasks')->default(false);
            $table->boolean('can_delete_tasks')->default(false);
            $table->boolean('can_assign_tasks')->default(false);
            $table->boolean('can_verify_tasks')->default(false);
            
            $table->boolean('can_view_staff')->default(false);
            $table->boolean('can_create_staff')->default(false);
            $table->boolean('can_edit_staff')->default(false);
            $table->boolean('can_delete_staff')->default(false);
            
            $table->boolean('can_view_reports')->default(false);
            $table->boolean('can_view_financial_reports')->default(false);
            
            $table->boolean('can_manage_permissions')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_permissions');
    }
};



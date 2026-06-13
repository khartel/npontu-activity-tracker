<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tracks every change made to an activity_log entry.
     * This provides a full audit trail for handover management.
     */
    public function up(): void
    {
        Schema::create('activity_log_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained('activity_logs')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('log_date');
            $table->enum('status_before', ['pending', 'in_progress', 'done', 'skipped'])->nullable();
            $table->enum('status_after', ['pending', 'in_progress', 'done', 'skipped']);
            $table->text('remark')->nullable();
            $table->integer('sms_system_count')->nullable();
            $table->integer('sms_log_count')->nullable();
            $table->integer('sms_discrepancy')->nullable();
            // Bio snapshot at time of update
            $table->string('personnel_name');
            $table->string('personnel_employee_id');
            $table->string('personnel_email');
            $table->string('personnel_department')->nullable();
            $table->timestamp('updated_at_time');
            $table->timestamps();

            $table->index(['activity_id', 'log_date']);
            $table->index(['user_id', 'log_date']);
            $table->index(['log_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log_history');
    }
};

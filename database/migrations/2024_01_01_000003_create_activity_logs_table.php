<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('log_date');
            $table->enum('status', ['pending', 'in_progress', 'done', 'skipped'])->default('pending');
            $table->text('remark')->nullable();
            // For SMS comparison type activities
            $table->integer('sms_system_count')->nullable()->comment('SMS count from system');
            $table->integer('sms_log_count')->nullable()->comment('SMS count from logs');
            $table->integer('sms_discrepancy')->nullable()->comment('Difference between system and log count');
            // Metadata
            $table->string('updated_by_name')->nullable()->comment('Snapshot of updater name');
            $table->string('updated_by_employee_id')->nullable()->comment('Snapshot of updater employee ID');
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();

            // Ensure one log per activity per day
            $table->unique(['activity_id', 'log_date'], 'unique_activity_daily_log');
            $table->index(['log_date']);
            $table->index(['user_id', 'log_date']);
            $table->index(['status', 'log_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityLogHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Update the status and remark of an activity log for a specific date.
     * Also captures a full history entry (bio snapshot + timestamp).
     */
    public function update(Request $request, ActivityLog $activityLog)
    {
        $validated = $request->validate([
            'status'           => ['required', 'in:pending,in_progress,done,skipped'],
            'remark'           => ['nullable', 'string', 'max:1000'],
            'sms_system_count' => ['nullable', 'integer', 'min:0'],
            'sms_log_count'    => ['nullable', 'integer', 'min:0'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $now  = now();

        // Calculate SMS discrepancy if both values are provided
        $discrepancy = null;
        if (isset($validated['sms_system_count']) && isset($validated['sms_log_count'])) {
            $discrepancy = $validated['sms_system_count'] - $validated['sms_log_count'];
        }

        DB::transaction(function () use ($activityLog, $validated, $user, $now, $discrepancy) {
            $previousStatus = $activityLog->status;

            // Update the main log entry
            $activityLog->update([
                'status'                => $validated['status'],
                'remark'                => $validated['remark'] ?? $activityLog->remark,
                'sms_system_count'      => $validated['sms_system_count'] ?? $activityLog->sms_system_count,
                'sms_log_count'         => $validated['sms_log_count'] ?? $activityLog->sms_log_count,
                'sms_discrepancy'       => $discrepancy ?? $activityLog->sms_discrepancy,
                'user_id'               => $user->id,
                'updated_by_name'       => $user->name,
                'updated_by_employee_id'=> $user->employee_id,
                'status_updated_at'     => $now,
            ]);

            // Write an immutable history record (bio snapshot + timestamp)
            ActivityLogHistory::create([
                'activity_log_id'       => $activityLog->id,
                'activity_id'           => $activityLog->activity_id,
                'user_id'               => $user->id,
                'log_date'              => $activityLog->log_date,
                'status_before'         => $previousStatus,
                'status_after'          => $validated['status'],
                'remark'                => $validated['remark'] ?? null,
                'sms_system_count'      => $validated['sms_system_count'] ?? null,
                'sms_log_count'         => $validated['sms_log_count'] ?? null,
                'sms_discrepancy'       => $discrepancy,
                'personnel_name'        => $user->name,
                'personnel_employee_id' => $user->employee_id,
                'personnel_email'       => $user->email,
                'personnel_department'  => $user->department,
                'updated_at_time'       => $now,
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Activity updated successfully.',
                'status'     => $activityLog->fresh()->status,
                'updated_at' => $now->format('H:i:s'),
                'updated_by' => $user->name,
            ]);
        }

        $date = $activityLog->log_date->toDateString();

        return redirect()->route('dashboard', ['date' => $date])
            ->with('success', 'Activity status updated.');
    }

    /**
     * Quick-update via AJAX (returns JSON).
     */
    public function quickUpdate(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'date'             => ['required', 'date'],
            'status'           => ['required', 'in:pending,in_progress,done,skipped'],
            'remark'           => ['nullable', 'string', 'max:1000'],
            'sms_system_count' => ['nullable', 'integer', 'min:0'],
            'sms_log_count'    => ['nullable', 'integer', 'min:0'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $now  = now();

        // Calculate SMS discrepancy
        $discrepancy = null;
        if (isset($validated['sms_system_count']) && isset($validated['sms_log_count'])) {
            $discrepancy = $validated['sms_system_count'] - $validated['sms_log_count'];
        }

        DB::transaction(function () use ($activity, $validated, $user, $now, $discrepancy) {
            $log = ActivityLog::firstOrCreate(
                ['activity_id' => $activity->id, 'log_date' => $validated['date']],
                ['status' => 'pending', 'user_id' => $user->id]
            );

            $previousStatus = $log->status;

            $log->update([
                'status'                => $validated['status'],
                'remark'                => $validated['remark'] ?? $log->remark,
                'sms_system_count'      => $validated['sms_system_count'] ?? $log->sms_system_count,
                'sms_log_count'         => $validated['sms_log_count'] ?? $log->sms_log_count,
                'sms_discrepancy'       => $discrepancy ?? $log->sms_discrepancy,
                'user_id'               => $user->id,
                'updated_by_name'       => $user->name,
                'updated_by_employee_id'=> $user->employee_id,
                'status_updated_at'     => $now,
            ]);

            ActivityLogHistory::create([
                'activity_log_id'       => $log->id,
                'activity_id'           => $activity->id,
                'user_id'               => $user->id,
                'log_date'              => $validated['date'],
                'status_before'         => $previousStatus,
                'status_after'          => $validated['status'],
                'remark'                => $validated['remark'] ?? null,
                'sms_system_count'      => $validated['sms_system_count'] ?? null,
                'sms_log_count'         => $validated['sms_log_count'] ?? null,
                'sms_discrepancy'       => $discrepancy,
                'personnel_name'        => $user->name,
                'personnel_employee_id' => $user->employee_id,
                'personnel_email'       => $user->email,
                'personnel_department'  => $user->department,
                'updated_at_time'       => $now,
            ]);
        });

        return response()->json([
            'success'    => true,
            'message'    => 'Updated successfully.',
            'updated_by' => $user->name,
            'updated_at' => $now->format('H:i'),
        ]);
    }
}

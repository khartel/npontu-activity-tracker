<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityLogHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show the reporting view with filter options.
     */
    public function index(Request $request)
    {
        $from       = $request->get('from', now()->startOfMonth()->toDateString());
        $to         = $request->get('to', now()->toDateString());
        $activityId = $request->get('activity_id');
        $userId     = $request->get('user_id');
        $status     = $request->get('status');

        // Validate date range
        $fromDate = Carbon::parse($from);
        $toDate   = Carbon::parse($to);

        if ($toDate->lt($fromDate)) {
            [$from, $to] = [$to, $from];
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        // Base query for logs in range
        $logsQuery = ActivityLog::with(['activity', 'user', 'history.user'])
            ->forDateRange($from, $to)
            ->orderBy('log_date', 'desc');

        if ($activityId) {
            $logsQuery->where('activity_id', $activityId);
        }

        if ($userId) {
            $logsQuery->where('user_id', $userId);
        }

        if ($status) {
            $logsQuery->byStatus($status);
        }

        $logs = $logsQuery->paginate(30)->withQueryString();

        // Summary statistics for the period
        $allLogs = ActivityLog::forDateRange($from, $to)->get();
        $stats = [
            'total'       => $allLogs->count(),
            'done'        => $allLogs->where('status', 'done')->count(),
            'pending'     => $allLogs->where('status', 'pending')->count(),
            'in_progress' => $allLogs->where('status', 'in_progress')->count(),
            'skipped'     => $allLogs->where('status', 'skipped')->count(),
            'days_covered'=> $fromDate->diffInDays($toDate) + 1,
        ];

        // For filter dropdowns
        $activities = Activity::active()->ordered()->get();
        $users      = User::where('is_active', true)->orderBy('name')->get();
        $statuses   = ActivityLog::$statuses;

        // Group logs by date for a day-by-day breakdown
        $logsByDate = $logs->getCollection()->groupBy(fn ($l) => $l->log_date->toDateString());

        return view('reports.index', compact(
            'logs',
            'logsByDate',
            'stats',
            'activities',
            'users',
            'statuses',
            'from',
            'to',
            'activityId',
            'userId',
            'status'
        ));
    }

    /**
     * Export report as CSV.
     */
    public function export(Request $request)
    {
        $from       = $request->get('from', now()->startOfMonth()->toDateString());
        $to         = $request->get('to', now()->toDateString());
        $activityId = $request->get('activity_id');
        $userId     = $request->get('user_id');
        $status     = $request->get('status');

        $query = ActivityLog::with(['activity', 'user'])
            ->forDateRange($from, $to)
            ->orderBy('log_date', 'asc');

        if ($activityId) $query->where('activity_id', $activityId);
        if ($userId)     $query->where('user_id', $userId);
        if ($status)     $query->byStatus($status);

        $logs = $query->get();

        $filename = "activity-report_{$from}_to_{$to}.csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // CSV headers
            fputcsv($handle, [
                'Date',
                'Activity',
                'Category',
                'Status',
                'Remark',
                'SMS System Count',
                'SMS Log Count',
                'SMS Discrepancy',
                'Updated By',
                'Employee ID',
                'Updated At',
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->log_date->format('Y-m-d'),
                    $log->activity->title ?? '—',
                    $log->activity->category_label ?? '—',
                    $log->status_label,
                    $log->remark ?? '',
                    $log->sms_system_count ?? '',
                    $log->sms_log_count ?? '',
                    $log->sms_discrepancy ?? '',
                    $log->updated_by_name ?? $log->user->name ?? '',
                    $log->updated_by_employee_id ?? '',
                    $log->status_updated_at ? $log->status_updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * History detail for a single activity across a date range.
     */
    public function activityHistory(Request $request, Activity $activity)
    {
        $from = $request->get('from', now()->subDays(30)->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $history = ActivityLogHistory::with('user')
            ->where('activity_id', $activity->id)
            ->forDateRange($from, $to)
            ->orderBy('updated_at_time', 'desc')
            ->paginate(50)
            ->withQueryString();

        return view('reports.activity_history', compact('activity', 'history', 'from', 'to'));
    }
}

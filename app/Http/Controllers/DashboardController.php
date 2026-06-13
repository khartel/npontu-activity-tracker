<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityLogHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard with today's activities and a daily overview.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $viewDate = Carbon::parse($date);

        // Load all active activities with today's log entries
        $activities = Activity::active()
            ->ordered()
            ->with(['activityLogs' => function ($q) use ($date) {
                $q->forDate($date)->with(['user', 'history.user']);
            }, 'creator'])
            ->get();

        // Ensure every activity has a log entry for the selected date
        foreach ($activities as $activity) {
            if ($activity->activityLogs->isEmpty()) {
                $log = ActivityLog::firstOrCreate(
                    ['activity_id' => $activity->id, 'log_date' => $date],
                    ['status' => 'pending', 'user_id' => Auth::id()]
                );
                $activity->setRelation('activityLogs', collect([$log]));
            }
        }

        // Summary counts
        $logs = ActivityLog::forDate($date)->get();
        $summary = [
            'total'       => $activities->count(),
            'done'        => $logs->where('status', 'done')->count(),
            'pending'     => $logs->where('status', 'pending')->count(),
            'in_progress' => $logs->where('status', 'in_progress')->count(),
            'skipped'     => $logs->where('status', 'skipped')->count(),
        ];

        // Full update history for this day (for the handover view)
        $dayHistory = ActivityLogHistory::with(['activity', 'user'])
            ->forDate($date)
            ->orderBy('updated_at_time', 'desc')
            ->get();

        $isToday = $viewDate->isToday();

        return view('activities.dashboard', compact(
            'activities',
            'summary',
            'dayHistory',
            'date',
            'viewDate',
            'isToday'
        ));
    }
}

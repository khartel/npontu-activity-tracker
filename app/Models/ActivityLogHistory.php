<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_log_id',
        'activity_id',
        'user_id',
        'log_date',
        'status_before',
        'status_after',
        'remark',
        'sms_system_count',
        'sms_log_count',
        'sms_discrepancy',
        'personnel_name',
        'personnel_employee_id',
        'personnel_email',
        'personnel_department',
        'updated_at_time',
    ];

    protected $casts = [
        'log_date'        => 'date',
        'updated_at_time' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function activityLog()
    {
        return $this->belongsTo(ActivityLog::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForDate($query, string $date)
    {
        return $query->where('log_date', $date);
    }

    public function scopeForDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('log_date', [$from, $to]);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}

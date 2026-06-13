<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
        'log_date',
        'status',
        'remark',
        'sms_system_count',
        'sms_log_count',
        'sms_discrepancy',
        'updated_by_name',
        'updated_by_employee_id',
        'status_updated_at',
    ];

    protected $casts = [
        'log_date'          => 'date',
        'status_updated_at' => 'datetime',
    ];

    // ─── Status Config ────────────────────────────────────────────────────────

    public static array $statuses = [
        'pending'     => ['label' => 'Pending',     'color' => 'yellow',  'icon' => 'clock'],
        'in_progress' => ['label' => 'In Progress', 'color' => 'blue',    'icon' => 'loader'],
        'done'        => ['label' => 'Done',         'color' => 'green',   'icon' => 'check-circle'],
        'skipped'     => ['label' => 'Skipped',      'color' => 'gray',    'icon' => 'minus-circle'],
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statuses[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statuses[$this->status]['color'] ?? 'gray';
    }

    public function getStatusIconAttribute(): string
    {
        return self::$statuses[$this->status]['icon'] ?? 'circle';
    }

    public function hasSmsData(): bool
    {
        return $this->sms_system_count !== null || $this->sms_log_count !== null;
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function history()
    {
        return $this->hasMany(ActivityLogHistory::class)->orderBy('created_at', 'desc');
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

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }
}

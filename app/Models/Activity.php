<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'is_recurring',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_active'    => 'boolean',
    ];

    // ─── Category Labels ──────────────────────────────────────────────────────

    public static array $categories = [
        'sms_monitoring'    => 'SMS Monitoring',
        'system_check'      => 'System Check',
        'log_review'        => 'Log Review',
        'incident_response' => 'Incident Response',
        'maintenance'       => 'Maintenance',
        'reporting'         => 'Reporting',
        'other'             => 'Other',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::$categories[$this->category] ?? ucfirst($this->category);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function logHistory()
    {
        return $this->hasMany(ActivityLogHistory::class);
    }

    /**
     * Get or create the activity log entry for today (or a specific date).
     */
    public function getDailyLog(string $date = null): ActivityLog
    {
        $date = $date ?? now()->toDateString();

        return $this->activityLogs()->firstOrCreate(
            ['log_date' => $date],
            ['status' => 'pending', 'user_id' => auth()->id()]
        );
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}

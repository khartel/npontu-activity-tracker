<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'employee_id',
        'email',
        'phone',
        'department',
        'role',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function activityLogHistory()
    {
        return $this->hasMany(ActivityLogHistory::class);
    }

    public function createdActivities()
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPersonnel(): bool
    {
        return $this->role === 'personnel';
    }

    public function getBioSnapshot(): array
    {
        return [
            'personnel_name'        => $this->name,
            'personnel_employee_id' => $this->employee_id,
            'personnel_email'       => $this->email,
            'personnel_department'  => $this->department,
        ];
    }
}

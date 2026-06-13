<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────────────────────
        $admin = User::create([
            'name'        => 'Kwame Mensah',
            'employee_id' => 'NPT-001',
            'email'       => 'admin@npontu.com',
            'phone'       => '+233 55 654 1525',
            'department'  => 'Application Support',
            'role'        => 'admin',
            'password'    => Hash::make('password'),
            'is_active'   => true,
        ]);

        $personnel = [
            User::create([
                'name'        => 'Ama Asante',
                'employee_id' => 'NPT-002',
                'email'       => 'ama.asante@npontu.com',
                'phone'       => '+233 24 337 5520',
                'department'  => 'Application Support',
                'role'        => 'personnel',
                'password'    => Hash::make('password'),
                'is_active'   => true,
            ]),
            User::create([
                'name'        => 'Kofi Boateng',
                'employee_id' => 'NPT-003',
                'email'       => 'kofi.boateng@npontu.com',
                'phone'       => '+233 30 801 2328',
                'department'  => 'Application Support',
                'role'        => 'personnel',
                'password'    => Hash::make('password'),
                'is_active'   => true,
            ]),
        ];

        // ── Activities ─────────────────────────────────────────────────────────
        $activities = [
            [
                'title'       => 'Daily SMS Count vs Log Count',
                'description' => 'Compare the total SMS count in the system against the SMS count from server logs. Flag any discrepancy greater than 5.',
                'category'    => 'sms_monitoring',
                'sort_order'  => 1,
            ],
            [
                'title'       => 'Morning System Health Check',
                'description' => 'Verify all core services are running. Check CPU, memory, and disk usage on production servers.',
                'category'    => 'system_check',
                'sort_order'  => 2,
            ],
            [
                'title'       => 'Application Error Log Review',
                'description' => 'Review application error logs for new exceptions, 5xx errors, or recurring warnings.',
                'category'    => 'log_review',
                'sort_order'  => 3,
            ],
            [
                'title'       => 'Database Connection Pool Check',
                'description' => 'Monitor active DB connections, check for long-running queries, and confirm replication is healthy.',
                'category'    => 'system_check',
                'sort_order'  => 4,
            ],
            [
                'title'       => 'Afternoon SMS Count Reconciliation',
                'description' => 'Mid-day SMS count comparison between system and logs to catch issues early.',
                'category'    => 'sms_monitoring',
                'sort_order'  => 5,
            ],
            [
                'title'       => 'Incident & Ticket Queue Review',
                'description' => 'Check helpdesk queue for new incidents, update ticket statuses, and escalate where needed.',
                'category'    => 'incident_response',
                'sort_order'  => 6,
            ],
            [
                'title'       => 'Backup Verification',
                'description' => 'Confirm that overnight database and file backups completed successfully. Log backup sizes.',
                'category'    => 'maintenance',
                'sort_order'  => 7,
            ],
            [
                'title'       => 'End-of-Day Summary Report',
                'description' => 'Compile and send end-of-day summary to team leads. Include any pending issues for handover.',
                'category'    => 'reporting',
                'sort_order'  => 8,
            ],
        ];

        $activityModels = [];
        foreach ($activities as $data) {
            $activityModels[] = Activity::create([
                ...$data,
                'is_recurring' => true,
                'is_active'    => true,
                'created_by'   => $admin->id,
            ]);
        }

        // ── Sample logs for the last 7 days ───────────────────────────────────
        $allUsers = array_merge([$admin], $personnel);
        $statuses = ['pending', 'in_progress', 'done', 'done', 'done']; // weight toward done

        for ($d = 6; $d >= 1; $d--) {
            $date = now()->subDays($d)->toDateString();

            foreach ($activityModels as $activity) {
                $user   = $allUsers[array_rand($allUsers)];
                $status = $statuses[array_rand($statuses)];

                $data = [
                    'activity_id'            => $activity->id,
                    'user_id'                => $user->id,
                    'log_date'               => $date,
                    'status'                 => $status,
                    'remark'                 => $status === 'done' ? 'Completed without issues.' : ($status === 'pending' ? null : 'In progress — monitoring.'),
                    'updated_by_name'        => $user->name,
                    'updated_by_employee_id' => $user->employee_id,
                    'status_updated_at'      => Carbon::parse($date)->addHours(rand(8, 17)),
                ];

                if ($activity->category === 'sms_monitoring') {
                    $sysCount       = rand(4500, 5500);
                    $logCount       = $sysCount + rand(-20, 20);
                    $data['sms_system_count'] = $sysCount;
                    $data['sms_log_count']    = $logCount;
                    $data['sms_discrepancy']  = $sysCount - $logCount;
                }

                ActivityLog::create($data);
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceResult;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Support\AttendancePresenter;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $school = SchoolSetting::query()->firstOrFail();
        $today = now($school->timezone);
        $dateStr = $today->toDateString();

        // Caching stats for 30 seconds to prevent "dump load" on frequent refreshes
        $statsData = Cache::remember('admin_dashboard_stats_' . $dateStr, 30, function () use ($dateStr) {
            $totalStudents = User::query()->where('role', UserRole::SISWA->value)->where('active', true)->count();
            $totalTeachers = User::query()->where('role', UserRole::GURU->value)->where('active', true)->count();
            $presentStudents = Attendance::query()
                ->whereDate('attendance_date', $dateStr)
                ->where('result', AttendanceResult::SUCCESS->value)
                ->whereHas('user', fn ($query) => $query->where('role', UserRole::SISWA->value))
                ->count();

            return compact('totalStudents', 'totalTeachers', 'presentStudents');
        });

        $totalStudents = $statsData['totalStudents'];
        $totalTeachers = $statsData['totalTeachers'];
        $presentStudents = $statsData['presentStudents'];
        $absentStudents = max(0, $totalStudents - $presentStudents);

        // Fetch real teacher rankings based on "Discipline" (Earliest Average Arrival Time)
        // Calculated for the current month only. Resets automatically every month.
        $startOfMonth = now($school->timezone)->startOfMonth();
        $endOfMonth = now($school->timezone)->endOfMonth();

        $teacherRankings = User::query()
            ->where('role', UserRole::GURU->value)
            ->where('active', true)
            ->whereHas('attendances', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('result', AttendanceResult::SUCCESS->value)
                      ->whereBetween('scanned_at', [$startOfMonth, $endOfMonth]);
            })
            ->withCount(['attendances' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('result', AttendanceResult::SUCCESS->value)
                      ->whereBetween('scanned_at', [$startOfMonth, $endOfMonth]);
            }])
            ->withAvg(['attendances as avg_arrival_time' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('result', AttendanceResult::SUCCESS->value)
                      ->whereBetween('scanned_at', [$startOfMonth, $endOfMonth]);
            }], 'scanned_at')
            ->orderBy('avg_arrival_time', 'asc') // Earliest average time wins
            ->limit(5)
            ->get()
            ->map(function($user) {
                // Convert avg_arrival_time (which might be a string timestamp) to a clean time format
                $user->formatted_avg_time = $user->avg_arrival_time ? \Carbon\Carbon::parse($user->avg_arrival_time)->format('H:i') : '--:--';
                return $user;
            });

        return view('admin.dashboard', [
            'school' => $school,
            'activeUser' => auth()->user(),
            'teacherRankings' => $teacherRankings,
            'stats' => [
                ['label' => 'Total siswa', 'value' => $totalStudents, 'caption' => 'data pengguna aktif', 'icon' => 'ti-school', 'tone' => 'purple'],
                ['label' => 'Total guru', 'value' => $totalTeachers, 'caption' => 'data pengguna aktif', 'icon' => 'ti-users', 'tone' => 'blue'],
                ['label' => 'Hadir hari ini', 'value' => $presentStudents, 'caption' => AttendancePresenter::percentage($presentStudents, $totalStudents) . ' dari total', 'icon' => 'ti-circle-check', 'tone' => 'success'],
                ['label' => 'Belum hadir', 'value' => $absentStudents, 'caption' => 'menunggu', 'icon' => 'ti-clock', 'tone' => 'warning'],
            ],
            'recentScans' => Attendance::query()->with('user')->latest('scanned_at')->limit(5)->get(),
            'todayLabel' => ucfirst($today->locale('id')->translatedFormat('l, d F Y')) . ' · ' . $today->format('H:i') . ' WIB',
            'summary' => [
                'present' => $presentStudents,
                'absent' => $absentStudents,
                'percentage' => AttendancePresenter::percentage($presentStudents, $totalStudents),
            ],
        ]);
    }
}

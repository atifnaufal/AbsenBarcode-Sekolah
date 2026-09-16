<?php
namespace App\Http\Controllers;

use App\Enums\AttendanceResult;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $school = SchoolSetting::firstOrFail();
        $today = now($school->timezone)->toDateString();
        $monthStart = now($school->timezone)->startOfMonth()->toDateString();
        $monthEnd = now($school->timezone)->endOfMonth()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->first();

        $monthAttendances = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->orderBy('attendance_date', 'desc')
            ->get();

        $stats = [
            'today' => [
                'status' => $todayAttendance?->result->value ?? 'belum_absen',
                'time' => $todayAttendance?->scanned_at?->format('H:i') ?? '-',
                'distance' => $todayAttendance?->distance_meters ? round($todayAttendance->distance_meters) . 'm' : '-',
            ],
            'month' => [
                'total' => $monthAttendances->count(),
                'hadir' => $monthAttendances->where('result', AttendanceResult::SUCCESS)->count(),
                'terlambat' => $monthAttendances->whereIn('result', [AttendanceResult::EXPIRED, AttendanceResult::DUPLICATE])->count(),
                'di_luar' => $monthAttendances->where('result', AttendanceResult::OUTSIDE_AREA)->count(),
            ],
        ];

        $recentScans = Attendance::where('user_id', $user->id)
            ->with('token.school')
            ->latest('scanned_at')
            ->limit(5)
            ->get();

        $teacherRankings = collect();
        if ($user->role === UserRole::GURU) {
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
                ->orderBy('avg_arrival_time', 'asc')
                ->limit(5)
                ->get()
                ->map(function($u) {
                    $u->formatted_avg_time = $u->avg_arrival_time ? \Carbon\Carbon::parse($u->avg_arrival_time)->format('H:i') : '--:--';
                    return $u;
                });
        }

        return view('student.dashboard', compact('user', 'school', 'stats', 'recentScans', 'teacherRankings'))
            ->with('hideNav', true);
    }

    public function scan(): View
    {
        $school = SchoolSetting::firstOrFail();
        $token = app(\App\Services\QrTokenService::class)->activeOrIssue($school);
        return view('attendance.scan', [
            'demoQrToken' => $token->getAttribute('plain_token'),
            'activeUser' => auth()->user(),
            'hideNav' => true,
        ]);
    }

    public function profile(): View
    {
        $user = auth()->user();
        return view('student.profile', compact('user'))
            ->with('hideNav', true);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'identifier' => 'required|string|max:30|unique:users,identifier,' . $user->id,
            'class_name' => 'nullable|string|max:50',
            'password' => 'nullable|min:6|confirmed',
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        // If password was updated, refresh the session hash to prevent logout
        if (isset($data['password'])) {
            auth()->login($user);
        }

        return back()->with('ok', 'Profil dan sesi Anda berhasil diperbarui');
    }
}

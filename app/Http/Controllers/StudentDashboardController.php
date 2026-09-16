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
                ->orderBy('attendances_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($u) use ($startOfMonth, $endOfMonth) {
                    $avgSeconds = $u->attendances()
                        ->where('result', AttendanceResult::SUCCESS->value)
                        ->whereBetween('scanned_at', [$startOfMonth, $endOfMonth])
                        ->get()
                        ->avg(fn($a) => $a->scanned_at->diffInSeconds($a->scanned_at->copy()->startOfDay()));

                    $u->formatted_avg_time = $avgSeconds ? now()->startOfDay()->addSeconds($avgSeconds)->format('H:i') : '--:--';
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

        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $data = $request->validate($rules);

        // ✅ FIX: Only update avatar if a new file is actually uploaded
        if ($request->hasFile('avatar')) {
            $disk = config('filesystems.default') === 'cloudinary' ? 'cloudinary' : 'public';

            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk($disk)->exists($user->avatar)) {
                \Storage::disk($disk)->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', $disk);
        } else {
            // Remove avatar from data array so it doesn't overwrite existing value with null
            unset($data['avatar']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = \Hash::make($data['password']);
        }

        $user->update($data);

        if (isset($data['password'])) {
            auth()->login($user);
        }

        return back()->with('ok', 'Profil dan sesi Anda berhasil diperbarui');
    }
}

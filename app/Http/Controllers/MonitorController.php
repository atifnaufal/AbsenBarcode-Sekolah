<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceResult;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Services\QrTokenService;
use App\Support\AttendancePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MonitorController extends Controller
{
    public function __construct(private readonly QrTokenService $tokens)
    {
    }

    public function index(): View
    {
        $school = SchoolSetting::query()->firstOrFail();
        $isClosed = $this->isScheduleClosed($school);
        $token = $isClosed ? null : $this->tokens->activeOrIssue($school);

        return view('monitor.index', [
            'school' => $school,
            'activeQr' => $token ? $this->tokens->payload($token) : null,
            'summary' => $this->summary($school),
            'isClosed' => $isClosed,
            'hideNav' => true, // ✅ NEW: Hide Admin Sidebar/Topbar on Monitor
            'schedule' => [
                'start' => $school->attendance_start,
                'end' => $school->attendance_end,
                'label' => $school->attendance_label ?? 'Absensi',
            ]
        ]);
    }

    public function refresh(): JsonResponse
    {
        $school = SchoolSetting::query()->firstOrFail();
        if ($this->isScheduleClosed($school)) {
            return response()->json(['ok' => true, 'qr' => null, 'is_closed' => true]);
        }
        $token = $this->tokens->activeOrIssue($school);
        return response()->json(['ok'=>true,'qr'=>$this->tokens->payload($token), 'is_closed' => false]);
    }

    private function isScheduleClosed(SchoolSetting $school): bool
    {
        // ✅ NEW: QR ONLY appears if start AND end time are set.
        if (empty($school->attendance_start) || empty($school->attendance_end)) {
            return true;
        }

        $now = now($school->timezone)->format('H:i:s');

        // If current time is outside the window, it's closed.
        return $now < $school->attendance_start || $now > $school->attendance_end;
    }

    public function recentScans(): JsonResponse
    {
        $school = SchoolSetting::query()->firstOrFail();
        $today = now($school->timezone)->toDateString();
        $scans = Attendance::with('user')->whereDate('attendance_date',$today)->latest('scanned_at')->limit(10)->get()->map(fn($a)=>[
            'id'=>$a->id,'name'=>$a->user->name,'identifier'=>$a->user->identifier,'class'=>$a->user->class_name,'time'=>$a->scanned_at?->format('H:i'),'result'=>$a->result->value,
        ]);
        return response()->json(['scans'=>$scans,'summary'=>$this->summary($school)]);
    }

    /** @return array<string, int|string> */
    private function summary(SchoolSetting $school): array
    {
        $today = now($school->timezone)->toDateString();
        $total = User::query()->where('role', UserRole::SISWA->value)->where('active', true)->count();
        $present = Attendance::query()
            ->whereDate('attendance_date', $today)
            ->where('result', AttendanceResult::SUCCESS->value)
            ->whereHas('user', fn ($query) => $query->where('role', UserRole::SISWA->value))
            ->count();

        return [
            'present' => $present,
            'absent' => max(0, $total - $present),
            'percentage' => AttendancePresenter::percentage($present, $total),
        ];
    }
}

<?php

namespace App\Services;

use App\Enums\AttendanceResult;
use App\Models\Attendance;
use App\Models\AttendanceToken;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Support\GeoDistance;
use Illuminate\Support\Facades\DB;

class AttendanceValidationService
{
    public function __construct(private readonly FirebaseAttendancePublisher $firebase)
    {
    }

    /** @return array<string, mixed> */
    public function scan(User $user, string $rawToken, float $latitude, float $longitude, ?float $accuracy): array
    {
        $steps = $this->steps();
        $token = AttendanceToken::query()->with('school')->where('token_hash', hash('sha256', $rawToken))->first();

        if (! $token || ! $token->isValid()) {
            $steps['qr'] = $this->step('QR valid', 'Token sudah kedaluwarsa atau tidak dikenali', 'failed');

            return $this->failure(AttendanceResult::EXPIRED, 'QR sudah kedaluwarsa. Pindai kode terbaru yang sedang tampil di monitor.', $steps);
        }

        $steps['qr'] = $this->step('QR valid', 'Token masih aktif', 'passed');
        $school = $token->school;

        // ✅ NEW: Check Schedule Window
        if ($school->attendance_start && $school->attendance_end) {
            $nowTime = now($school->timezone)->format('H:i:s');
            if ($nowTime < $school->attendance_start || $nowTime > $school->attendance_end) {
                return $this->failure(AttendanceResult::UNAVAILABLE, "Sesi {$school->attendance_label} sudah berakhir atau belum dimulai.", $steps);
            }
        }

        $distance = GeoDistance::meters($latitude, $longitude, $school->latitude, $school->longitude);

        if ($accuracy !== null && $accuracy > config('attendance.max_accuracy_meters')) {
            $steps['location'] = $this->step('Lokasi sesuai', 'Akurasi GPS belum cukup untuk memproses absensi', 'failed');

            return $this->failure(AttendanceResult::OUTSIDE_AREA, 'Lokasi belum cukup akurat. Periksa GPS dan coba lagi.', $steps, $distance);
        }

        if ($distance > $school->radius_meters) {
            $steps['location'] = $this->step('Lokasi sesuai', 'Perangkat berada di luar radius sekolah', 'failed');

            return $this->failure(AttendanceResult::OUTSIDE_AREA, 'Anda berada di luar area sekolah. Absensi hanya dapat dilakukan di dalam radius lokasi sekolah.', $steps, $distance);
        }

        $steps['location'] = $this->step('Lokasi sesuai', "Dalam radius {$school->radius_meters} m", 'passed');
        $attendanceDate = now($school->timezone)->toDateString();
        $existing = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if ($existing) {
            $steps['attendance'] = $this->step('Status kehadiran', 'Kehadiran sudah tercatat hari ini', 'failed');

            return $this->failure(AttendanceResult::DUPLICATE, 'Kehadiran sudah tercatat. Anda tidak perlu melakukan pemindaian ulang hari ini.', $steps, $distance, $existing);
        }

        $steps['attendance'] = $this->step('Status kehadiran', 'Belum tercatat, siap disimpan', 'passed');
        $attendance = DB::transaction(function () use ($user, $token, $attendanceDate, $latitude, $longitude, $accuracy, $distance) {
            $locked = Attendance::query()
                ->where('user_id', $user->id)
                ->whereDate('attendance_date', $attendanceDate)
                ->lockForUpdate()
                ->first();

            if ($locked) {
                return null;
            }

            return Attendance::query()->create([
                'user_id' => $user->id,
                'attendance_token_id' => $token->id,
                'attendance_date' => $attendanceDate,
                'scanned_at' => now($token->school->timezone),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'accuracy_meters' => $accuracy,
                'distance_meters' => $distance,
                'result' => AttendanceResult::SUCCESS,
                'session_label' => $token->school->attendance_label,
            ]);
        });

        if (! $attendance) {
            $steps['attendance'] = $this->step('Status kehadiran', 'Kehadiran sudah tercatat hari ini', 'failed');

            return $this->failure(AttendanceResult::DUPLICATE, 'Kehadiran sudah tercatat. Sistem tidak membuat catatan kedua.', $steps, $distance);
        }

        $steps['recorded'] = $this->step('Pencatatan', 'Data tersimpan di server', 'passed');

        \App\Jobs\PublishAttendanceToFirebase::dispatch($attendance);

        return [
            'ok' => true,
            'result' => AttendanceResult::SUCCESS->value,
            'message' => 'Kehadiran berhasil dicatat. Waktu kehadiran Anda telah tersimpan.',
            'steps' => $steps,
            'attendance' => array_merge($attendance->toArray(), [
                'user_name' => $attendance->user->name,
                'identifier' => $attendance->user->identifier,
                'scanned_at' => $attendance->scanned_at?->toIso8601String(),
            ]),
            'distance_meters' => round($distance, 2),
        ];
    }

    /** @return array<string, array<string, string>> */
    private function steps(): array
    {
        return [
            'qr' => $this->step('QR valid', 'Menunggu pemeriksaan', 'waiting'),
            'location' => $this->step('Lokasi sesuai', 'Menunggu pemeriksaan', 'waiting'),
            'attendance' => $this->step('Status kehadiran', 'Menunggu pemeriksaan', 'waiting'),
            'recorded' => $this->step('Pencatatan', 'Menunggu proses', 'waiting'),
        ];
    }

    /** @return array<string, string> */
    private function step(string $label, string $detail, string $status): array
    {
        return compact('label', 'detail', 'status');
    }

    /** @return array<string, mixed> */
    private function failure(AttendanceResult $result, string $message, array $steps, ?float $distance = null, ?Attendance $attendance = null): array
    {
        return [
            'ok' => false,
            'result' => $result->value,
            'message' => $message,
            'steps' => $steps,
            'attendance' => $attendance,
            'distance_meters' => $distance === null ? null : round($distance, 2),
        ];
    }
}

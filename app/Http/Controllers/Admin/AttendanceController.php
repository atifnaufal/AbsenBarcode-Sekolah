<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceResult;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'result' => 'required|string',
            'session_label' => 'nullable|string|max:100',
        ]);

        // Prevent duplicate attendance for same user on same day
        $exists = Attendance::where('user_id', $data['user_id'])
            ->whereDate('attendance_date', $data['attendance_date'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anggota sudah memiliki rekaman kehadiran pada tanggal tersebut.');
        }

        Attendance::create([
            'user_id' => $data['user_id'],
            'attendance_date' => $data['attendance_date'],
            'result' => $data['result'],
            'session_label' => $data['session_label'],
            'scanned_at' => $data['attendance_date'] === now()->toDateString() ? now() : \Carbon\Carbon::parse($data['attendance_date'])->startOfDay(),
            'attendance_token_id' => null, // Allowed by new migration
            'latitude' => null,
            'longitude' => null,
            'distance_meters' => null,
        ]);

        return back()->with('ok', 'Status kehadiran berhasil dicatat secara manual.');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'result' => 'nullable|string',
            'session_label' => 'nullable|string|max:100',
        ]);

        $attendance->update(array_filter($data));

        return back()->with('ok', 'Data absensi ' . $attendance->user->name . ' berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('ok', 'Rekaman absensi telah dihapus dari sistem.');
    }
}

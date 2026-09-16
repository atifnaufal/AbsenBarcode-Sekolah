<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceResult;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'result' => 'required|string',
            'session_label' => 'nullable|string|max:100',
        ]);

        $attendance->update($data);

        return back()->with('ok', 'Data absensi ' . $attendance->user->name . ' berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('ok', 'Rekaman absensi telah dihapus dari sistem.');
    }
}

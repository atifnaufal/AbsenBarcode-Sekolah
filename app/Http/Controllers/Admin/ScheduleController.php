<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSchedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = SchoolSchedule::orderBy('start_time')->get();
        return view('admin.schedules.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        SchoolSchedule::create($data);
        return back()->with('ok', 'Jadwal agenda berhasil ditambahkan.');
    }

    public function update(Request $request, SchoolSchedule $schedule)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'active' => 'boolean',
        ]);

        $schedule->update($data);
        return back()->with('ok', 'Jadwal agenda diperbarui.');
    }

    public function destroy(SchoolSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('ok', 'Jadwal agenda dihapus.');
    }
}

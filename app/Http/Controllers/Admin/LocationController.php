<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
class LocationController extends Controller{
    public function edit(){ $school=SchoolSetting::firstOrFail(); return view('admin.location.edit', compact('school')); }
    public function update(Request $r){
        $data=$r->validate([
            'name'=>'required|string|max:100',
            'latitude'=>'required|numeric|between:-90,90',
            'longitude'=>'required|numeric|between:-180,180',
            'radius_meters'=>'required|integer|min:10|max:5000',
            'address'=>'nullable|string|max:255',
            'attendance_start'=>'nullable|date_format:H:i',
            'attendance_end'=>'nullable|date_format:H:i',
            'attendance_label'=>'nullable|string|max:100',
        ]);
        if (!empty($data['attendance_start']) && substr_count($data['attendance_start']) === 5) {
            $data['attendance_start'] .= ':00';
        }
        if (!empty($data['attendance_end']) && substr_count($data['attendance_end']) === 5) {
            $data['attendance_end'] .= ':00';
        }
        SchoolSetting::firstOrFail()->update($data);
        return back()->with('ok','Pengaturan lokasi dan jadwal QR diperbarui');
    }
}

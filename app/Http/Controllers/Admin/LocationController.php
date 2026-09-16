<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
class LocationController extends Controller{
    public function edit(){ $school=SchoolSetting::firstOrFail(); return view('admin.location.edit', compact('school')); }
    public function update(Request $r){
        try {
            $data = $r->validate([
                'name' => 'required|string|max:100',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius_meters' => 'required|integer|min:10|max:5000',
                'address' => 'nullable|string|max:255',
                'attendance_start' => 'nullable',
                'attendance_end' => 'nullable',
                'attendance_label' => 'nullable|string|max:100',
            ]);

            // Ensure consistent time format H:i:s for Database
            foreach (['attendance_start', 'attendance_end'] as $field) {
                if (!empty($data[$field])) {
                    if (strlen($data[$field]) === 5) {
                        $data[$field] .= ':00';
                    }
                } else {
                    $data[$field] = null;
                }
            }

            $school = SchoolSetting::first();
            if ($school) {
                $school->update($data);
            } else {
                SchoolSetting::create($data);
            }

            return back()->with('ok', 'Seluruh konfigurasi berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error("Error updating location: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }
}

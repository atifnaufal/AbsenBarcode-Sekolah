<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\UserRole;

class ReportController extends Controller{
    public function index(Request $r){
        $date = $r->input('date', now()->toDateString());
        $filterType = $r->input('filter_type', 'day');
        $role = $r->input('role', 'siswa'); // Default to siswa as requested
        $className = $r->input('class_name');

        $userQuery = User::query()
            ->where('active', true)
            ->where('role', $role === 'guru' ? UserRole::GURU->value : UserRole::SISWA->value);

        // If Siswa, allow filtering by class. If Guru, the user said "all user -> role guru"
        if ($role === 'siswa' && $className) {
            $userQuery->where('class_name', $className);
        }

        $usersCount = (clone $userQuery)->count();

        // 🟢 FIX: Correctly calculate present count using whereHas for better performance and accuracy
        $presentCountQuery = (clone $userQuery)->whereHas('attendances', function($q) use ($date, $filterType) {
            if ($filterType === 'month') {
                $q->whereMonth('attendance_date', date('m', strtotime($date)))->whereYear('attendance_date', date('Y', strtotime($date)));
            } elseif ($filterType === 'year') {
                $q->whereYear('attendance_date', date('Y', strtotime($date)));
            } elseif ($filterType === 'semester') {
                $m = date('m', strtotime($date)); $y = date('Y', strtotime($date));
                $m >= 7 ? $q->whereBetween('attendance_date', ["$y-07-01", "$y-12-31"]) : $q->whereBetween('attendance_date', ["$y-01-01", "$y-06-30"]);
            } else {
                $q->whereDate('attendance_date', $date);
            }
        });

        $presentCount = $presentCountQuery->count();

        $users = $userQuery->with(['attendances' => function($q) use ($date, $filterType) {
                if ($filterType === 'month') {
                    $q->whereMonth('attendance_date', date('m', strtotime($date)))->whereYear('attendance_date', date('Y', strtotime($date)));
                } elseif ($filterType === 'year') {
                    $q->whereYear('attendance_date', date('Y', strtotime($date)));
                } elseif ($filterType === 'semester') {
                    $m = date('m', strtotime($date)); $y = date('Y', strtotime($date));
                    $m >= 7 ? $q->whereBetween('attendance_date', ["$y-07-01", "$y-12-31"]) : $q->whereBetween('attendance_date', ["$y-01-01", "$y-06-30"]);
                } else {
                    $q->whereDate('attendance_date', $date);
                }
            }])
            ->orderBy('name')
            ->paginate(30)->withQueryString();

        $classes = User::where('role', UserRole::SISWA)->whereNotNull('class_name')->distinct()->pluck('class_name');
        $reportTitle = $this->getReportTitle($date, $filterType);

        return view('admin.reports.index', compact('users', 'date', 'filterType', 'reportTitle', 'role', 'className', 'classes', 'usersCount', 'presentCount'));
    }

    public function updateKeterangan(Request $r, Attendance $attendance)
    {
        $r->validate(['session_label' => 'nullable|string|max:100']);
        $attendance->update(['session_label' => $r->session_label]);
        return back()->with('ok', 'Keterangan berhasil diperbarui.');
    }

    public function export(Request $r){
        $date = $r->input('date', now()->toDateString());
        $filterType = $r->input('filter_type', 'day');
        $format = $r->input('format', 'csv');
        $role = $r->input('role', 'siswa');
        $className = $r->input('class_name');

        $rows = User::query()
            ->where('active', true)
            ->where('role', $role === 'guru' ? UserRole::GURU : UserRole::SISWA)
            ->when($role === 'siswa' && $className, fn($q) => $q->where('class_name', $className))
            ->with(['attendances' => function($q) use ($date, $filterType) {
                if ($filterType === 'month') {
                    $q->whereMonth('attendance_date', date('m', strtotime($date)))->whereYear('attendance_date', date('Y', strtotime($date)));
                } elseif ($filterType === 'year') {
                    $q->whereYear('attendance_date', date('Y', strtotime($date)));
                } elseif ($filterType === 'semester') {
                    $m = date('m', strtotime($date)); $y = date('Y', strtotime($date));
                    $m >= 7 ? $q->whereBetween('attendance_date', ["$y-07-01", "$y-12-31"]) : $q->whereBetween('attendance_date', ["$y-01-01", "$y-06-30"]);
                } else {
                    $q->whereDate('attendance_date', $date);
                }
            }])->get();

        $reportTitle = $this->getReportTitle($date, $filterType);

        if ($format === 'pdf') {
            return view('admin.reports.print', compact('rows', 'date', 'reportTitle'));
        }

        if ($format === 'excel') {
            $output = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
            $output .= "<head><meta charset=\"utf-8\"></head><body>";
            $output .= "<h2>LAPORAN KEHADIRAN DIGITAL SMK BINA UTAMA KENDAL</h2>";
            $output .= "<h4>$reportTitle</h4>";
            $output .= "<table border=\"1\"><tr style=\"background-color: #2c68f5; color: #ffffff;\"><th>Nama</th><th>ID</th><th>Kelas/Grup</th><th>Waktu</th><th>Status</th><th>Keterangan</th></tr>";
            foreach($rows as $u) {
                $a = $u->attendances->first();
                $time = $a ? ($a->scanned_at ? $a->scanned_at->format('H:i:s') : '--:--') : '-';
                $status = $a ? ($a->result->value === 'success' ? 'Hadir' : 'Telat/Lainnya') : 'Tidak Hadir';
                $label = $a ? $a->session_label : '-';
                $output .= "<tr><td>{$u->name}</td><td>'{$u->identifier}</td><td>{$u->class_name}</td><td>{$time}</td><td>{$status}</td><td>{$label}</td></tr>";
            }
            $output .= "</table></body></html>";
            return response($output, 200, ['Content-Type' => 'application/vnd.ms-excel', 'Content-Disposition' => "attachment; filename=laporan.xls"]);
        }

        $csv="Nama,ID,Kelas/Grup,Waktu,Status,Keterangan\n";
        foreach($rows as $u) {
            $a = $u->attendances->first();
            $time = $a ? ($a->scanned_at ? $a->scanned_at->format('H:i:s') : '') : '';
            $status = $a ? 'Hadir' : 'Tidak Hadir';
            $label = $a ? $this->sanitizeCsv($a->session_label) : '';
            $csv.="\"{$u->name}\",{$u->identifier},\"{$u->class_name}\",{$time},{$status},\"{$label}\"\n";
        }
        return response($csv,200,['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=laporan.csv"]);
    }

    private function getReportTitle(string $date, string $filterType): string
    {
        $dt = \Carbon\Carbon::parse($date)->locale('id');
        return match ($filterType) {
            'month' => 'Bulan ' . $dt->translatedFormat('F Y'),
            'year' => 'Tahun ' . $dt->translatedFormat('Y'),
            'semester' => 'Semester ' . ($dt->month >= 7 ? 'Ganjil' : 'Genap') . ' ' . $dt->translatedFormat('Y'),
            default => 'Tanggal ' . $dt->translatedFormat('l, d F Y'),
        };
    }

    private function sanitizeCsv(?string $value): string
    {
        $value = (string) ($value ?? '');
        if (in_array(substr($value, 0, 1), ['=', '+', '-', '@', '|', '%'])) {
            $value = "\t" . $value;
        }
        return $value;
    }
}

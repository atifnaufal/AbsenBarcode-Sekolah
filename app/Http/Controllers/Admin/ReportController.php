<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
class ReportController extends Controller{
    public function index(Request $r){
        $date = $r->input('date', now()->toDateString());
        $filterType = $r->input('filter_type', 'day'); // day, month, year, semester

        $query = Attendance::with('user');

        if ($filterType === 'month') {
            $query->whereMonth('attendance_date', date('m', strtotime($date)))
                  ->whereYear('attendance_date', date('Y', strtotime($date)));
        } elseif ($filterType === 'year') {
            $query->whereYear('attendance_date', date('Y', strtotime($date)));
        } elseif ($filterType === 'semester') {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            if ($month >= 7) { // Ganjil
                $query->whereBetween('attendance_date', ["$year-07-01", "$year-12-31"]);
            } else { // Genap
                $query->whereBetween('attendance_date', ["$year-01-01", "$year-06-30"]);
            }
        } else {
            $query->whereDate('attendance_date', $date);
        }

        $attendances = $query->orderBy('scanned_at', 'desc')->paginate(20)->withQueryString();

        $reportTitle = $this->getReportTitle($date, $filterType);

        return view('admin.reports.index', compact('attendances', 'date', 'filterType', 'reportTitle'));
    }

    public function export(Request $r){
        // ... (existing export logic start)
        $date = $r->input('date', now()->toDateString());
        $filterType = $r->input('filter_type', 'day');
        $format = $r->input('format', 'csv');

        $query = Attendance::with('user');

        if ($filterType === 'month') {
            $query->whereMonth('attendance_date', date('m', strtotime($date)))
                  ->whereYear('attendance_date', date('Y', strtotime($date)));
        } elseif ($filterType === 'year') {
            $query->whereYear('attendance_date', date('Y', strtotime($date)));
        } elseif ($filterType === 'semester') {
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            if ($month >= 7) {
                $query->whereBetween('attendance_date', ["$year-07-01", "$year-12-31"]);
            } else {
                $query->whereBetween('attendance_date', ["$year-01-01", "$year-06-30"]);
            }
        } else {
            $query->whereDate('attendance_date', $date);
        }

        $rows = $query->get();
        $reportTitle = $this->getReportTitle($date, $filterType);

        if ($format === 'pdf') {
            return view('admin.reports.print', compact('rows', 'date', 'reportTitle'));
        }

        if ($format === 'excel') {
            $output = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
            $output .= "<head><meta charset=\"utf-8\"></head><body>";
            $output .= "<h2>LAPORAN KEHADIRAN DIGITAL SMK BINA UTAMA KENDAL</h2>";
            $output .= "<h4>$reportTitle</h4>";
            // ... rest of excel ...

            $output .= "<table border=\"1\">";
            $output .= "<tr style=\"background-color: #2c68f5; color: #ffffff; font-weight: bold;\">";
            $output .= "<th>Nama Lengkap</th><th>NISN / Nomor Induk</th><th>Grup / Kelas</th><th>Keterangan</th><th>Waktu Scan</th><th>Hasil</th>";
            $output .= "</tr>";
            foreach($rows as $a) {
                $time = $a->scanned_at ? $a->scanned_at->format('H:i:s') : '--:--';
                $name = htmlspecialchars((string) $a->user?->name, ENT_QUOTES, 'UTF-8');
                $id = htmlspecialchars((string) $a->user?->identifier, ENT_QUOTES, 'UTF-8');
                $cls = htmlspecialchars((string) $a->user?->class_name, ENT_QUOTES, 'UTF-8');
                $label = htmlspecialchars((string) $a->session_label, ENT_QUOTES, 'UTF-8');
                $output .= "<tr><td>{$name}</td><td>{$id}</td><td>{$cls}</td><td>{$label}</td><td>{$time}</td><td>{$a->result->value}</td></tr>";
            }
            $output .= "</table></body></html>";
            return response($output, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=laporan-$date.xls",
                'Cache-Control' => 'max-age=0'
            ]);
        }

        $csv="Nama,NISN,Kelas,Keterangan,Waktu,Hasil\n";
        foreach($rows as $a) {
            $time = $a->scanned_at ? $a->scanned_at->format('H:i:s') : '';
            $name = $this->sanitizeCsv($a->user?->name);
            $id = $a->user?->identifier;
            $cls = $this->sanitizeCsv($a->user?->class_name);
            $label = $this->sanitizeCsv($a->session_label);
            $csv.="\"{$name}\",{$id},\"{$cls}\",\"{$label}\",{$time},{$a->result->value}\n";
        }
        return response($csv,200,['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=laporan-$date.csv"]);
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

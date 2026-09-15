<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran Digital - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 13px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #0f1e3d;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        .meta-info {
            margin-bottom: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f2f5fa;
            color: #0f1e3d;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-late { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .footer-sig {
            margin-top: 50px;
            float: right;
            text-align: center;
            width: 200px;
        }
        .footer-sig p { margin: 0; }
        .space { height: 70px; }
        @media print {
            .no-print { display: none; }
            body { margin: 10px; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; background: #e3e8f0; padding: 10px; border-radius: 8px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2c68f5; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨 Cetak / Simpan sebagai PDF
        </button>
        <p style="margin: 5px 0 0 0; font-size: 11px; color: #555;">Gunakan opsi "Save as PDF" di dialog cetak browser Anda untuk mengunduh dokumen PDF.</p>
    </div>

    <div class="header">
        <h1>SMK BINA UTAMA KENDAL</h1>
        <p>Alamat: Jl. Raya Utama, Kabupaten Kendal, Jawa Tengah</p>
        <p><strong>LAPORAN REKAPITULASI KEHADIRAN HARIAN DIGITAL</strong></p>
    </div>

    <div class="meta-info">
        Tanggal Rekapitulasi: {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5px;">No</th>
                <th>Nama Lengkap</th>
                <th>NISN / Nomor Induk</th>
                <th>Grup / Kelas</th>
                <th>Keterangan</th>
                <th>Waktu Pemindaian</th>
                <th>Status Hasil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $a)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $u = $a->user?->name }}</strong></td>
                    <td>{{ $a->user?->identifier }}</td>
                    <td>{{ $a->user?->class_name ?? 'Staf / Guru' }}</td>
                    <td>{{ $a->session_label ?? '-' }}</td>
                    <td>{{ $a->scanned_at ? $a->scanned_at->format('H:i:s') : '--:--' }} WIB</td>
                    <td>
                        @if($a->result->value === 'success')
                            <span class="badge badge-success">Hadir</span>
                        @elseif($a->result->value === 'late')
                            <span class="badge badge-late">Terlambat</span>
                        @else
                            <span class="badge badge-danger">{{ $a->result->value }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #8a95a8;">Tidak ada rekaman kehadiran pada tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-sig">
        <p>Kendal, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
        <p>Kepala Sekolah,</p>
        <div class="space"></div>
        <p><strong>____________________</strong></p>
        <p>NIP. -</p>
    </div>

    <script>
        // Automatically open the print/PDF dialog box upon load
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>

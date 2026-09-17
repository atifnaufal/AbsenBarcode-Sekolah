<?php

namespace App\Enums;

enum AttendanceResult: string
{
    case SUCCESS = 'success';
    case EXPIRED = 'expired';
    case OUTSIDE_AREA = 'outside_area';
    case DUPLICATE = 'duplicate';
    case UNAVAILABLE = 'unavailable';
    case PERMISSION = 'permission';
    case SICK = 'sick';
    case ABSENT = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::SUCCESS => 'Tercatat (Hadir)',
            self::EXPIRED => 'QR Kedaluwarsa',
            self::OUTSIDE_AREA => 'Di Luar Area',
            self::DUPLICATE => 'Sudah Tercatat',
            self::UNAVAILABLE => 'Belum Tersedia',
            self::PERMISSION => 'Izin',
            self::SICK => 'Sakit',
            self::ABSENT => 'Tidak Hadir (Alfa)',
        };
    }
}

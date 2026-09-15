<?php

namespace App\Models;

use App\Enums\AttendanceResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_token_id',
        'attendance_date',
        'scanned_at',
        'latitude',
        'longitude',
        'accuracy_meters',
        'distance_meters',
        'result',
        'failure_reason',
        'session_label',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'scanned_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'accuracy_meters' => 'float',
            'distance_meters' => 'float',
            'result' => AttendanceResult::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(AttendanceToken::class, 'attendance_token_id');
    }
}

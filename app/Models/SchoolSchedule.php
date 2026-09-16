<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSchedule extends Model
{
    protected $fillable = [
        'label',
        'start_time',
        'end_time',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }
}

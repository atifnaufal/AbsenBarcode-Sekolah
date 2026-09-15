<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->time('attendance_start')->nullable()->comment('Jam mulai absen');
            $table->time('attendance_end')->nullable()->comment('Jam akhir/terlambat absen');
            $table->string('attendance_label')->nullable()->default('Absen Masuk Sekolah');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('session_label')->nullable()->after('result');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['attendance_start', 'attendance_end', 'attendance_label']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('session_label');
        });
    }
};

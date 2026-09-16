<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (SchoolSetting::query()->count() === 0) {
            SchoolSetting::query()->create([
                'name' => 'SMK BINA UTAMA KENDAL',
                'address' => 'Jl. Raya Utama, Kabupaten Kendal',
                'latitude' => -6.9182000,
                'longitude' => 110.2056000,
                'radius_meters' => 80,
                'timezone' => 'Asia/Jakarta',
            ]);
        }

        User::query()->updateOrCreate(
            ['email' => 'adminsekolah@example.test'],
            [
                'name' => 'Admin Sekolah',
                'password' => 'password',
                'role' => UserRole::ADMIN_SEKOLAH,
                'identifier' => 'ADM-001',
                'class_name' => null,
                'guardian_name' => null,
                'active' => true,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'guru@example.test'],
            [
                'name' => 'Ahmad Fauzan',
                'password' => 'password',
                'role' => UserRole::GURU,
                'identifier' => 'GURU-001',
                'class_name' => 'Wali kelas XI TKJ 1',
                'guardian_name' => null,
                'active' => true,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'siswa@example.test'],
            [
                'name' => 'Nadia Putri',
                'password' => 'password',
                'role' => UserRole::SISWA,
                'identifier' => '2403107',
                'class_name' => 'XI TKJ 1',
                'guardian_name' => 'Rina Putri',
                'active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}

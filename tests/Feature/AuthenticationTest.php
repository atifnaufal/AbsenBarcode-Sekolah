<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test users with different roles
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        User::factory()->create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@sekolah.test',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN_SEKOLAH,
            'active' => true,
        ]);

        // Create guru user
        User::factory()->create([
            'name' => 'Guru Demo',
            'email' => 'guru@sekolah.test',
            'password' => bcrypt('password'),
            'role' => UserRole::GURU,
            'active' => true,
        ]);

        // Create siswa user
        User::factory()->create([
            'name' => 'Siswa Demo',
            'email' => 'siswa@sekolah.test',
            'password' => bcrypt('password'),
            'role' => UserRole::SISWA,
            'active' => true,
        ]);
    }

    /**
     * ✅ TEST 1: Admin can login and redirects to dashboard
     */
    public function test_admin_login_redirects_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@sekolah.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'admin@sekolah.test')->first());
    }

    /**
     * ✅ TEST 2: Guru can login and redirects to student dashboard (shared)
     */
    public function test_guru_login_redirects_to_student_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'guru@sekolah.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'guru@sekolah.test')->first());
    }

    /**
     * ✅ TEST 3: Siswa can login and redirects to student dashboard (shared)
     */
    public function test_siswa_login_redirects_to_student_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'siswa@sekolah.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'siswa@sekolah.test')->first());
    }

    /**
     * ✅ TEST 4: Siswa cannot access admin dashboard
     */
    public function test_siswa_cannot_access_admin_dashboard(): void
    {
        $siswa = User::where('email', 'siswa@sekolah.test')->first();

        $response = $this->actingAs($siswa)->get('/dashboard');

        $response->assertStatus(403);
    }

    /**
     * ✅ TEST 5: Guru cannot access admin dashboard
     */
    public function test_guru_cannot_access_admin_dashboard(): void
    {
        $guru = User::where('email', 'guru@sekolah.test')->first();

        $response = $this->actingAs($guru)->get('/dashboard');

        $response->assertStatus(403);
    }

    /**
     * ✅ TEST 6: Admin can access dashboard
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('email', 'admin@sekolah.test')->first();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_unified_login_page(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/login/mahasiswa')->assertStatus(200);
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_admin_login_auto_detects_role_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin Prodi',
            'username' => 'adminprodi',
            'email' => 'admin@pradita.ac.id',
            'normalized_email' => 'admin@pradita.ac.id',
            'password' => Hash::make('secret123'),
            'role' => 'admin_forta',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'username' => 'adminprodi',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'web');
    }

    public function test_lecturer_login_by_nip_or_email_auto_detects_role_and_redirects_to_lecturer_portal(): void
    {
        // Create Lecturer without pre-existing User to test auto-provisioning & NIP login
        $lecturer = Lecturer::create([
            'nip' => '199001012020',
            'name' => 'Dr. Budi Dosen',
            'email' => 'budi.dosen@pradita.ac.id',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'username' => '199001012020',
            'password' => '199001012020', // Default password = NIP when auto-provisioned
        ]);

        $response->assertRedirect(route('lecturer.defenses.internship.my-defenses'));
        $this->assertAuthenticated('web');
        $this->assertEquals('dosen', auth('web')->user()->role);

        // Lecturer accessing /admin/dashboard is redirected to lecturer portal
        $this->get('/admin/dashboard')
            ->assertRedirect(route('lecturer.defenses.internship.my-defenses'));
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_guest_can_view_student_login_page(): void
    {
        $response = $this->get('/login/mahasiswa');

        $response->assertStatus(200);
    }

    public function test_guest_can_view_admin_login_page(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }
}

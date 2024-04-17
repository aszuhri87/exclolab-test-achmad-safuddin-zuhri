<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function register_test(): void
    {
        $response = $this->post('/register');

        $response->assertStatus(200);
    }

    public function login_test(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function logout_test(): void
    {
        $response = $this->get('/logout');

        $response->assertStatus(200);
    }

}

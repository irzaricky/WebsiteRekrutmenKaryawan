<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_registrasi_dapat_ditampilkan()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_pengguna_baru_dapat_mendaftar()
    {
        $response = $this->post('/register', [
            'name' => 'Pengguna Uji',
            'email' => 'uji@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_pengguna_tidak_dapat_mendaftar_dengan_data_tidak_valid()
    {
        $dataRegistrasi = [
            'name' => '',
            'email' => 'email-tidak-valid',
            'password' => 'password',
            'password_confirmation' => 'password_berbeda',
        ];

        $response = $this->post('/register', $dataRegistrasi);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }
}

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
            'name' => 'Pengguna Uji 1',
            'email' => 'uji@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_pengguna_tidak_dapat_mendaftar_dengan_data_email_tidak_valid()
    {
        $dataRegistrasi = [
            'name' => 'Pengguna Uji 2',
            'email' => 'email-tidak-valid',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $dataRegistrasi);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_pengguna_tidak_dapat_mendaftar_dengan_data_password_tidak_sama()
    {
        $dataRegistrasi = [
            'name' => 'Pengguna Uji 3',
            'email' => 'uji@example.com',
            'password' => 'password',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $dataRegistrasi);

        $response->assertSessionHasErrors(['password']);
    }
    public function test_pengguna_tidak_dapat_mendaftar_dengan_data_nama_kosong()
    {
        $dataRegistrasi = [
            'name' => '',
            'email' => 'uji@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $dataRegistrasi);

        $response->assertSessionHasErrors(['name']);
    }
}

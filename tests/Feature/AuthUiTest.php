<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthUiTest extends TestCase
{
    public function test_public_layout_renders_on_home_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('GOOD COFFEE. GOOD COMPANY.', false);
        $response->assertSee('See you at Nabe', false);
        $response->assertSee('Reservasi Sekarang', false);
        $response->assertSee('COME SAY HELLO', false);
        $response->assertSee('EXPLORE', false);
        $response->assertSee('Kusumanegara', false);
    }

    public function test_login_page_renders_with_required_fields(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('WELCOME BACK.', false);
        $response->assertSee('NABE / BEHIND THE COUNTER', false);
        $response->assertSee('GOOD DAYS.', false);
        $response->assertSee('WELL PLANNED.', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="remember"', false);
        $response->assertSee('Masuk ke Panel', false);
    }

    public function test_two_factor_challenge_page_renders_with_code_field(): void
    {
        $response = $this->get('/two-factor-challenge');

        $response->assertStatus(200);
        $response->assertSee('VERIFIKASI 2FA.', false);
        $response->assertSee('name="code"', false);
        $response->assertSee('name="recovery_code"', false);
        $response->assertSee('Google Authenticator', false);
        $response->assertSee('Verifikasi Kode', false);
    }

    public function test_two_factor_setup_page_renders_with_qr_and_backup_codes(): void
    {
        $response = $this->get('/two-factor-setup');

        $response->assertStatus(200);
        $response->assertSee('AKTIVASI 2FA.', false);
        $response->assertSee('Pindai QR Code di Google Authenticator', false);
        $response->assertSee('Simpan 8 Kode Cadangan Darurat', false);
        $response->assertSee('name="code"', false);
        $response->assertSee('Konfirmasi &amp; Aktifkan 2FA', false);
    }
}

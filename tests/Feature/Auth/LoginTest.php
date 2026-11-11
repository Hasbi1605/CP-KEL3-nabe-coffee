<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('login:owner@nabe.coffee|127.0.0.1');
    }

    public function test_guest_can_view_login_page(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('WELCOME BACK.');
    }

    public function test_login_requires_valid_email_and_password(): void
    {
        $this->post('/login', [])
            ->assertSessionHasErrors([
                'email' => 'Email wajib diisi.',
                'password' => 'Kata sandi wajib diisi.',
            ]);

        $this->post('/login', [
            'email' => 'bukan-email',
            'password' => 'secret',
        ])->assertSessionHasErrors([
            'email' => 'Format email tidak valid.',
        ]);
    }

    public function test_owner_can_login_and_is_redirected(): void
    {
        $owner = User::factory()->owner()->create([
            'email' => 'owner@nabe.coffee',
            'password' => 'password-rahasia',
        ]);

        $response = $this->post('/login', [
            'email' => 'owner@nabe.coffee',
            'password' => 'password-rahasia',
        ]);

        $this->assertAuthenticatedAs($owner);
        // Sejak tugas 1.5, login kata sandi yang benar lanjut ke 2FA dulu;
        // user tanpa 2FA diarahkan ke aktivasi.
        $response->assertRedirect(route('two-factor.setup'));
    }

    public function test_branch_admin_can_login(): void
    {
        $admin = User::factory()->branchAdmin()->create([
            'email' => 'admin-kusumanegara@nabe.coffee',
            'password' => 'password-rahasia',
        ]);

        $this->post('/login', [
            'email' => 'admin-kusumanegara@nabe.coffee',
            'password' => 'password-rahasia',
        ])->assertRedirect(route('two-factor.setup'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_wrong_credentials_show_indonesian_error(): void
    {
        User::factory()->owner()->create([
            'email' => 'owner@nabe.coffee',
            'password' => 'password-benar',
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'owner@nabe.coffee',
            'password' => 'password-salah',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Email atau kata sandi salah.',
            ]);

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        User::factory()->owner()->create([
            'email' => 'owner@nabe.coffee',
            'password' => 'password-benar',
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->post('/login', [
                'email' => 'owner@nabe.coffee',
                'password' => 'salah-'.$attempt,
            ])->assertSessionHasErrors('email');
        }

        $response = $this->post('/login', [
            'email' => 'owner@nabe.coffee',
            'password' => 'password-benar',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringStartsWith(
            'Terlalu banyak percobaan login. Coba lagi dalam ',
            session('errors')->getBag('default')->first('email')
        );

        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_login_is_redirected(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get('/login')
            ->assertRedirect(route('home'));
    }

    public function test_user_can_logout(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->post('/logout')
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Lu sudah keluar. Sampai jumpa lagi.');

        $this->assertGuest();
    }

    public function test_guest_cannot_logout(): void
    {
        $this->post('/logout')->assertRedirect(route('login'));
    }

    public function test_role_middleware_blocks_wrong_role_and_allows_matching_role(): void
    {
        $owner = User::factory()->owner()->create();
        $admin = User::factory()->branchAdmin()->create();

        Route::middleware(['web', 'auth', 'role:owner'])
            ->get('/_test-owner-only', fn () => 'ok-owner');

        $this->actingAs($admin)->get('/_test-owner-only')->assertForbidden();
        $this->actingAs($owner)->get('/_test-owner-only')->assertOk()->assertSee('ok-owner');

        // Sesi tes masih memegang owner dari actingAs sebelumnya; tamu
        // asli (tanpa sesi login) terverifikasi di tes login/logout lain
        // dan oleh middleware `auth` bawaan di route ini.
        auth()->logout();
        $this->get('/_test-owner-only')->assertRedirect(route('login'));
    }
}

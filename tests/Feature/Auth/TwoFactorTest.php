<?php

namespace Tests\Feature\Auth;

use App\Models\Branch;
use App\Models\User;
use App\Services\Auth\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private TwoFactorService $twoFactor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->twoFactor = app(TwoFactorService::class);
    }

    private function userWithConfirmedTwoFactor(array $overrides = []): User
    {
        $setup = $this->twoFactor->generateSetup();

        return User::factory()->owner()->create(array_merge([
            'email' => 'owner@nabe.coffee',
            'password' => 'password-benar',
            'two_factor_secret' => $setup['secret'],
            'two_factor_recovery_codes' => $setup['recovery_codes'],
            'two_factor_confirmed_at' => now(),
        ], $overrides));
    }

    public function test_login_without_two_factor_redirects_to_setup_with_qr_and_recovery_codes(): void
    {
        User::factory()->owner()->create([
            'email' => 'owner@nabe.coffee',
            'password' => 'password-benar',
        ]);

        $this->post('/login', [
            'email' => 'owner@nabe.coffee',
            'password' => 'password-benar',
        ])->assertRedirect(route('two-factor.setup'));

        $this->get('/two-factor-setup')
            ->assertOk()
            ->assertSee('AKTIVASI 2FA.')
            ->assertSee('<svg', false)
            ->assertSee('Simpan 8 Kode Cadangan Darurat');
    }

    public function test_user_can_confirm_two_factor_with_valid_totp(): void
    {
        $user = User::factory()->owner()->create([
            'email' => 'owner@nabe.coffee',
            'password' => 'password-benar',
        ]);

        $this->actingAs($user)->get('/two-factor-setup')->assertOk();
        $pending = session('two_factor.pending');
        $code = $this->twoFactor->getCurrentTotp($pending['secret']);

        $this->post('/two-factor-setup', ['code' => $code])
            ->assertRedirect(route('home'))
            ->assertSessionHas('status');

        $user->refresh();
        $this->assertNotNull($user->two_factor_confirmed_at);
        $this->assertCount(8, $user->two_factor_recovery_codes);
        $this->assertTrue(session('two_factor.passed'));
    }

    public function test_wrong_confirmation_code_is_rejected(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)->get('/two-factor-setup')->assertOk();

        $this->post('/two-factor-setup', ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertNull($user->fresh()->two_factor_confirmed_at);
    }

    public function test_login_with_two_factor_requires_valid_challenge_code(): void
    {
        $user = $this->userWithConfirmedTwoFactor();
        $code = $this->twoFactor->getCurrentTotp($user->two_factor_secret);

        $this->post('/login', [
            'email' => 'owner@nabe.coffee',
            'password' => 'password-benar',
        ])->assertRedirect(route('two-factor.login'));

        $this->post('/two-factor-challenge', ['code' => '000000'])
            ->assertSessionHasErrors(['code' => 'Kode autentikasi salah.']);

        $this->post('/two-factor-challenge', ['code' => $code])
            ->assertRedirect(route('home'));

        $this->assertTrue(session('two_factor.passed'));
    }

    public function test_recovery_code_works_once_and_is_consumed(): void
    {
        $user = $this->userWithConfirmedTwoFactor();
        $recoveryCode = $user->two_factor_recovery_codes[0];

        $this->actingAs($user)->post('/two-factor-challenge', [
            'recovery_code' => $recoveryCode,
        ])->assertRedirect(route('home'));

        $user->refresh();
        $this->assertCount(7, $user->two_factor_recovery_codes);
        $this->assertNotContains($recoveryCode, $user->two_factor_recovery_codes);

        // Kode yang sama tidak bisa dipakai dua kali.
        session()->forget('two_factor.passed');
        $this->post('/two-factor-challenge', ['recovery_code' => $recoveryCode])
            ->assertSessionHasErrors('recovery_code');
    }

    public function test_challenge_is_locked_for_sixty_seconds_after_five_wrong_codes(): void
    {
        $user = $this->userWithConfirmedTwoFactor();
        $key = 'two-factor:'.$user->id.'|127.0.0.1';
        RateLimiter::clear($key);

        $this->actingAs($user);

        foreach (range(1, 5) as $attempt) {
            $this->post('/two-factor-challenge', ['code' => '00000'.$attempt])
                ->assertSessionHasErrors('code');
        }

        $response = $this->post('/two-factor-challenge', [
            'code' => $this->twoFactor->getCurrentTotp($user->two_factor_secret),
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertStringStartsWith(
            'Terlalu banyak kode salah. Login dikunci ',
            session('errors')->getBag('default')->first('code')
        );
    }

    public function test_two_factor_middleware_redirects_unverified_and_allows_verified(): void
    {
        Route::middleware(['web', 'auth', 'two-factor'])->get('/_test-protected', fn () => 'ok-protected');

        $unconfirmed = User::factory()->owner()->create();
        $this->actingAs($unconfirmed)->get('/_test-protected')
            ->assertRedirect(route('two-factor.setup'));

        $confirmed = $this->userWithConfirmedTwoFactor(['email' => 'lain@nabe.coffee']);
        $this->actingAs($confirmed)->get('/_test-protected')
            ->assertRedirect(route('two-factor.login'));

        $this->withSession(['two_factor.passed' => true])
            ->get('/_test-protected')
            ->assertOk()
            ->assertSee('ok-protected');
    }

    public function test_owner_can_reset_branch_admin_two_factor_but_admin_cannot_reset_owner(): void
    {
        $owner = $this->userWithConfirmedTwoFactor();
        $admin = $this->userWithConfirmedTwoFactor([
            'email' => 'admin@nabe.coffee',
            'role' => 'branch_admin',
            'branch_id' => Branch::factory(),
        ]);

        $this->actingAs($owner)->post(route('admin.users.reset-two-factor', $admin))
            ->assertRedirect()
            ->assertSessionHas('status');

        $admin->refresh();
        $this->assertNull($admin->two_factor_secret);
        $this->assertNull($admin->two_factor_confirmed_at);

        // Admin cabang tidak boleh me-reset 2FA owner.
        $adminConfirmed = $this->userWithConfirmedTwoFactor([
            'email' => 'admin2@nabe.coffee',
            'role' => 'branch_admin',
            'branch_id' => Branch::factory(),
        ]);
        $this->actingAs($adminConfirmed)->post(route('admin.users.reset-two-factor', $owner))
            ->assertForbidden();

        $this->assertNotNull($owner->fresh()->two_factor_secret);
    }

    public function test_guest_cannot_access_two_factor_pages(): void
    {
        $this->get('/two-factor-setup')->assertRedirect(route('login'));
        $this->get('/two-factor-challenge')->assertRedirect(route('login'));
    }

    public function test_old_auth_ui_tests_still_pass_for_authenticated_unconfirmed_pages(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)->get('/two-factor-setup')
            ->assertOk()
            ->assertSee('AKTIVASI 2FA.')
            ->assertSee('Simpan 8 Kode Cadangan Darurat');

        $confirmed = $this->userWithConfirmedTwoFactor(['email' => 'ui@nabe.coffee']);
        $this->actingAs($confirmed)->get('/two-factor-challenge')
            ->assertOk()
            ->assertSee('VERIFIKASI 2FA.')
            ->assertSee('name="code"', false)
            ->assertSee('name="recovery_code"', false);
    }
}

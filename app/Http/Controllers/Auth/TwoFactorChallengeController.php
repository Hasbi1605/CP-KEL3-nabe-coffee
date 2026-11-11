<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TwoFactorCodeRequest;
use App\Models\User;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor) {}

    public function create(Request $request): View|RedirectResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $this->twoFactor->hasConfirmedTwoFactor($user)) {
            return redirect()->route('two-factor.setup');
        }

        if ($request->session()->get('two_factor.passed') === true) {
            return redirect()->intended(AuthenticatedSessionController::redirectPathFor($user));
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * @throws ValidationException
     */
    public function store(TwoFactorCodeRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $throttleKey = 'two-factor:'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'code' => "Terlalu banyak kode salah. Login dikunci {$seconds} detik lagi.",
            ]);
        }

        $code = trim((string) $request->input('code', ''));
        $recoveryCode = trim((string) $request->input('recovery_code', ''));

        $valid = $code !== ''
            ? $this->twoFactor->verifyTotp($user->two_factor_secret, $code)
            : $this->twoFactor->consumeRecoveryCode($user, $recoveryCode);

        if (! $valid) {
            RateLimiter::hit($throttleKey, 60);

            $field = $code !== '' ? 'code' : 'recovery_code';
            $message = $code !== ''
                ? 'Kode autentikasi salah.'
                : 'Kode cadangan salah atau sudah dipakai.';

            throw ValidationException::withMessages([$field => $message]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->put('two_factor.passed', true);
        $request->session()->regenerate();

        return redirect()->intended(AuthenticatedSessionController::redirectPathFor($user))
            ->with('status', 'Verifikasi 2FA berhasil. Selamat datang kembali.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TwoFactorCodeRequest;
use App\Models\User;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorSetupController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor) {}

    public function create(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($this->twoFactor->hasConfirmedTwoFactor($user)) {
            return redirect()->intended(AuthenticatedSessionController::redirectPathFor($user))
                ->with('status', '2FA lu sudah aktif.');
        }

        $pending = $request->session()->get('two_factor.pending');

        if (! is_array($pending) || empty($pending['secret'])) {
            $pending = $this->twoFactor->generateSetup();
            $request->session()->put('two_factor.pending', $pending);
        }

        return view('auth.two-factor-setup', [
            'secretKey' => $pending['secret'],
            'qrCodeSvg' => $this->twoFactor->qrCodeSvg($user, $pending['secret']),
            'qrCodeUrl' => null,
            'recoveryCodes' => $pending['recovery_codes'],
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(TwoFactorCodeRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $pending = $request->session()->get('two_factor.pending');

        if (! is_array($pending) || empty($pending['secret'])) {
            return redirect()->route('two-factor.setup')
                ->with('error', 'Sesi aktivasi 2FA sudah habis. Coba lagi ya.');
        }

        if (! $this->twoFactor->verifyTotp($pending['secret'], (string) $request->input('code'))) {
            throw ValidationException::withMessages([
                'code' => 'Kode konfirmasi salah. Pastikan waktu di perangkat lu akurat.',
            ]);
        }

        $user->forceFill([
            'two_factor_secret' => $pending['secret'],
            'two_factor_recovery_codes' => $pending['recovery_codes'],
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('two_factor.pending');
        $request->session()->put('two_factor.passed', true);

        return redirect()->intended(AuthenticatedSessionController::redirectPathFor($user))
            ->with('status', '2FA berhasil diaktifkan. Simpan kode cadangan lu di tempat aman.');
    }
}

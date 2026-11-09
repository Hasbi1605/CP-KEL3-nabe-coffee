<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TwoFactorResetController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor) {}

    /**
     * Owner me-reset 2FA admin cabang yang kehilangan akses.
     */
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        $this->twoFactor->resetForUser($user);

        if ($request->user()?->is($user)) {
            $request->session()->forget(['two_factor.passed', 'two_factor.pending']);
        }

        return back()->with('status', "2FA untuk {$user->email} sudah direset. User itu wajib aktivasi ulang pas login berikutnya.");
    }
}

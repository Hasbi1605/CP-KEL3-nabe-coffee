<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\RedirectResponse;

class TwoFactorResetController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor) {}

    /**
     * Owner me-reset 2FA admin cabang yang kehilangan akses.
     */
    public function __invoke(User $user): RedirectResponse
    {
        abort_unless($user->isBranchAdmin(), 403, 'Hanya 2FA Admin Cabang yang dapat direset.');

        $this->twoFactor->resetForUser($user);

        return back()->with('status', "2FA untuk {$user->email} sudah direset. User itu wajib aktivasi ulang pas login berikutnya.");
    }
}

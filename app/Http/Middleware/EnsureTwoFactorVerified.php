<?php

namespace App\Http\Middleware;

use App\Services\Auth\TwoFactorService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function __construct(private TwoFactorService $twoFactor) {}

    /**
     * User yang sudah login tapi belum lolos 2FA diarahkan dulu ke
     * aktivasi (belum punya) atau challenge (sudah punya).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        if (! $this->twoFactor->hasConfirmedTwoFactor($user)) {
            return redirect()->guest(route('two-factor.setup'));
        }

        if ($request->session()->get('two_factor.passed') !== true) {
            return redirect()->guest(route('two-factor.login'));
        }

        return $next($request);
    }
}

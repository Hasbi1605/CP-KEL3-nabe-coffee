<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tujuan setelah login sesuai peran user.
     *
     * Dashboard admin belum tersedia di main sampai tugas 2.1 masuk;
     * sementara arahkan ke beranda publik agar alur tetap bisa diuji.
     */
    public static function redirectPathFor(?object $user): string
    {
        return match ($user->role ?? null) {
            UserRole::Owner->value => route('home'),
            UserRole::BranchAdmin->value => route('home'),
            default => route('home'),
        };
    }

    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(self::redirectPathFor(Auth::user()));
        }

        return view('auth.login');
    }

    /**
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(self::redirectPathFor(Auth::user()));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Lu sudah keluar. Sampai jumpa lagi.');
    }

    private function throttleKey(Request $request): string
    {
        $email = mb_strtolower((string) $request->input('email', ''));

        return 'login:'.$email.'|'.$request->ip();
    }
}

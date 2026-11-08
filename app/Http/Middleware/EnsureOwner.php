<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwner
{
    /**
     * Handle an incoming request.
     *
     * Hanya owner yang boleh masuk. Guest dan admin cabang ditolak
     * dengan 403 (tanpa redirect ke halaman login, karena route login
     * belum ada di tugas 1.2/1.3).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isOwner()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh owner.');
        }

        return $next($request);
    }
}

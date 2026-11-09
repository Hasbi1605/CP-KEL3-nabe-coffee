<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Pastikan user yang login punya salah satu role yang diizinkan.
     *
     * Dipakai sebagai middleware `role:owner` atau
     * `role:owner,branch_admin` di route admin.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        $role = $user->role instanceof UserRole ? $user->role->value : $user->role;

        if (! in_array($role, $roles, true)) {
            abort(403, 'Lu nggak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}

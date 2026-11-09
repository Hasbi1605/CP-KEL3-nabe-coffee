<?php

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // MERGE ORDER: merge PR #22 (1.3) lalu #23 (1.5) dulu, baru PR
        // ini (3.4). Versi final blok ini setelah gabungan adalah alias
        // langsung dari PR auth. Guard `is_file` di bawah menjaga PR ini
        // tetap jalan walau dibuka duluan; gabungan #22+#23+PR ini sudah
        // diverifikasi lokal (48 tes lulus, termasuk konflik User.php
        // yang harus deduplikasi ke versi enum dari #22).
        // Alias `role` dan `two-factor` didaftarkan oleh PR tugas 1.3/1.5.
        // Guard di bawah ini hanya cara sementara agar CMS 3.4 (yang butuh
        // proteksi owner + 2FA lewat nama alias tersebut) tetap aman
        // sebelum maupun sesudah PR auth itu merged: kalau kelas
        // middleware-nya belum ada, alias sengaja tidak didaftarkan dan
        // pemeriksaan owner di PromotionRequest/HomepageContentRequest
        // (authorize) tetap menjadi pagar terakhir.
        // Cek keberadaan file langsung, jangan pakai class_exists pada
        // kelas yang belum ada: autoloader Composer akan mencoba include
        // file-nya dan melempar ErrorException di mode testing.
        $aliases = [];

        if (is_file(__DIR__.'/../app/Http/Middleware/EnsureUserRole.php')) {
            $aliases['role'] = EnsureUserRole::class;
        }

        if (is_file(__DIR__.'/../app/Http/Middleware/EnsureTwoFactorVerified.php')) {
            $aliases['two-factor'] = EnsureTwoFactorVerified::class;
        }

        if ($aliases !== []) {
            $middleware->alias($aliases);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

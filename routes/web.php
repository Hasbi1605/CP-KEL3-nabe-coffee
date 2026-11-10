<?php

use App\Http\Controllers\Admin\HomepageContentController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Menu\MenuCatalogController;
use App\Models\Promotion;
use App\Services\Menu\HomepageContentService;
use Illuminate\Support\Facades\Route;

Route::get('/', function (HomepageContentService $content) {
    return view('welcome', [
        'homepageContent' => $content->all(),
        'promotions' => Promotion::activeNow()->with('branch')->orderByDesc('starts_at')->get(),
    ]);
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/two-factor-challenge', function () {
    return view('auth.two-factor-challenge');
})->name('two-factor.login');

Route::get('/two-factor-setup', function () {
    return view('auth.two-factor-setup');
})->name('two-factor.setup');

Route::get('/menu', [MenuCatalogController::class, 'index'])->name('menu.index');

$adminMiddleware = ['auth'];
if (is_file(__DIR__.'/../app/Http/Middleware/EnsureTwoFactorVerified.php')) {
    $adminMiddleware[] = 'two-factor';
}
if (is_file(__DIR__.'/../app/Http/Middleware/EnsureUserRole.php')) {
    $adminMiddleware[] = 'role:owner';
}
Route::middleware($adminMiddleware)->prefix('admin')->name('admin.')->group(function (): void {
    Route::resource('promotions', PromotionController::class);
    Route::get('homepage-content', [HomepageContentController::class, 'edit'])->name('homepage-content.edit');
    Route::put('homepage-content', [HomepageContentController::class, 'update'])->name('homepage-content.update');
});

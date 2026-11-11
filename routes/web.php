<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CapacitySlotController;
use App\Http\Controllers\Admin\HomepageContentController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\Auth\TwoFactorResetController;
use App\Http\Controllers\Auth\TwoFactorSetupController;
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

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'create'])->name('two-factor.login');
    Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'store'])->name('two-factor.challenge.store');
    Route::get('/two-factor-setup', [TwoFactorSetupController::class, 'create'])->name('two-factor.setup');
    Route::post('/two-factor-setup', [TwoFactorSetupController::class, 'store'])->name('two-factor.setup.store');

    Route::post('/admin/users/{user}/reset-two-factor', TwoFactorResetController::class)
        ->middleware(['role:owner', 'two-factor'])
        ->name('admin.users.reset-two-factor');
});

Route::middleware(['owner', 'two-factor'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::resource('branches', BranchController::class);
    Route::resource('menus', MenuController::class);
    // Parameter route disamakan dengan nama variabel di controller ($slot)
    // agar implicit model binding Laravel berjalan.
    Route::resource('capacity-slots', CapacitySlotController::class)
        ->parameters(['capacity-slots' => 'slot']);
});

Route::get('/admin/dashboard', function () {
    $role = request('role') === 'branch' ? 'branch' : 'owner';

    return view('admin.dashboard', ['role' => $role]);
})->middleware(['auth', 'two-factor'])->name('admin.dashboard');

Route::get('/menu', [MenuCatalogController::class, 'index'])->name('menu.index');

Route::middleware(['auth', 'role:owner', 'two-factor'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::resource('promotions', PromotionController::class);
    Route::get('homepage-content', [HomepageContentController::class, 'edit'])->name('homepage-content.edit');
    Route::put('homepage-content', [HomepageContentController::class, 'update'])->name('homepage-content.update');
});

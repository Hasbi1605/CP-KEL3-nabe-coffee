<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\Auth\TwoFactorResetController;
use App\Http\Controllers\Auth\TwoFactorSetupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
        ->middleware('role:owner')
        ->name('admin.users.reset-two-factor');
});

Route::get('/menu', function () {
    $branches = [
        'Nabe Coffee Kusumanegara',
        'Nabe Coffee Demangan',
        'Nabe Coffee Soragan',
        'Nabe Coffee Batikan (THE PADEL)',
    ];

    $menus = [
        [
            'name' => 'Eskopsu Lait',
            'category' => 'Kopi',
            'description' => 'Kopi susu untuk teman mulai hari.',
            'price' => 13000,
            'image' => 'images/menu/1461023058943-07fcbe16d735.jpg',
            'best_seller' => true,
            'promo' => true,
        ],
        [
            'name' => 'Matcha Latte',
            'category' => 'Non-Kopi',
            'description' => 'Flavored latte yang lembut dan creamy.',
            'price' => 25000,
            'image' => 'images/menu/1515823064-d6e0c04616a7.jpg',
            'best_seller' => true,
            'promo' => false,
        ],
        [
            'name' => 'Dancing Queen',
            'category' => 'Non-Kopi',
            'description' => 'Fizz series dengan rasa segar dan fruity.',
            'price' => 25000,
            'image' => 'images/menu/1497534446932-c925b458314e.jpg',
            'best_seller' => false,
            'promo' => false,
        ],
        [
            'name' => 'Caramel Croffle',
            'category' => 'Camilan',
            'description' => 'Crispy outside, happy inside.',
            'price' => 25000,
            'image' => 'images/menu/1484723091739-30a097e8f929.jpg',
            'best_seller' => true,
            'promo' => false,
        ],
        [
            'name' => 'Salt Bread',
            'category' => 'Camilan',
            'description' => 'Buttery, soft, dan sedikit gurih.',
            'price' => 18000,
            'image' => 'images/menu/1509440159596-0249088772ff.jpg',
            'best_seller' => false,
            'promo' => true,
        ],
    ];

    return view('menu.index', compact('branches', 'menus'));
})->name('menu.index');

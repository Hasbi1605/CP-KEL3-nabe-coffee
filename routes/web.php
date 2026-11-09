<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

Route::get('/admin/dashboard', function () {
    $role = request('role') === 'branch' ? 'branch' : 'owner';

    return view('admin.dashboard', ['role' => $role]);
})->name('admin.dashboard');

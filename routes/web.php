<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KoreksiController;
use App\Http\Controllers\MandorController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
Route::post('/daftar', [AuthController::class, 'register'])->name('register.submit');
Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/presensi/log', [PresensiController::class, 'log'])->name('presensi.log');
    Route::get('/presensi/log/export', [PresensiController::class, 'export'])->name('presensi.log.export');
    Route::get('/karyawan', [KaryawanController::class, 'halaman'])->name('karyawan.index');
    Route::get('/koreksi', [KoreksiController::class, 'index'])->name('koreksi.index');

    Route::post('/presensi/simpan', [PresensiController::class, 'simpan'])
        ->middleware('permission:presensi.input')->name('presensi.simpan');
    Route::get('/presensi/input', [PresensiController::class, 'inputForm'])
        ->middleware('permission:presensi.input')->name('presensi.input');

    Route::post('/koreksi', [KoreksiController::class, 'store'])
        ->middleware('permission:koreksi.request')->name('koreksi.store');
    Route::put('/koreksi/{koreksi}/approve', [KoreksiController::class, 'approve'])
        ->middleware('permission:koreksi.approve')->name('koreksi.approve');
    Route::put('/koreksi/{koreksi}/reject', [KoreksiController::class, 'reject'])
        ->middleware('permission:koreksi.approve')->name('koreksi.reject');

    Route::post('/karyawan', [KaryawanController::class, 'store'])
        ->middleware('permission:karyawan.create')->name('karyawan.store');
    Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])
        ->middleware('permission:karyawan.edit')->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])
        ->middleware('permission:karyawan.delete')->name('karyawan.destroy');

    Route::middleware('permission:mandor.manage')->group(function () {
        Route::get('/mandor', [MandorController::class, 'index'])->name('mandor.index');
        Route::post('/mandor', [MandorController::class, 'store'])->name('mandor.store');
        Route::delete('/mandor/{mandor}', [MandorController::class, 'destroy'])->name('mandor.destroy');
    });

    Route::middleware('superadmin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});
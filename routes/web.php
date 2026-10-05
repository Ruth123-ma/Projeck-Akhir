<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaterialMasukController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'dashboard' : 'login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [WarehouseController::class, 'dashboard'])->name('dashboard');
    Route::get('/absensi', [WarehouseController::class, 'absensi'])->name('absensi.index');
    Route::get('/absensi-karyawan', [WarehouseController::class, 'absensiKaryawan'])->name('absensi-karyawan');
    Route::get('/absensi/create', [WarehouseController::class, 'createAbsensi'])->name('absensi.create');
    Route::post('/absensi', [WarehouseController::class, 'storeAbsensi'])->name('absensi.store');
    Route::patch('/absensi/{attendance}/pulang', [WarehouseController::class, 'clockOut'])->name('absensi.clock-out');
    Route::get('/input-material', [WarehouseController::class, 'inputMaterial'])->name('input-material');
    Route::post('/input-material', [WarehouseController::class, 'storeTransaction'])->name('input-material.store');
    Route::get('/output-material', [WarehouseController::class, 'outputMaterial'])->name('output-material');
    Route::post('/output-material', [WarehouseController::class, 'storeTransaction'])->name('output-material.store');
    Route::get('/daftar-stok', [WarehouseController::class, 'daftarStok'])->name('daftar-stok');
    Route::get('/material/create', [WarehouseController::class, 'createMaterial'])->name('material.create');
    Route::post('/material', [WarehouseController::class, 'storeMaterial'])->name('material.store');
    Route::get('/material/{material}/edit', [WarehouseController::class, 'editMaterial'])->name('material.edit');
    Route::put('/material/{material}', [WarehouseController::class, 'updateMaterial'])->name('material.update');
    Route::get('/laporan-material', [WarehouseController::class, 'laporanMaterial'])->name('laporan-material');
    Route::get('/laporan-stok', [WarehouseController::class, 'laporanStok'])->name('laporan-stok');
    Route::get('/laporan', [WarehouseController::class, 'laporan'])->name('laporan.index');

    Route::get('/material-masuk', [MaterialMasukController::class, 'index'])->name('material-masuk.index');
    Route::post('/material-masuk', [MaterialMasukController::class, 'store'])->name('material-masuk.store');
});

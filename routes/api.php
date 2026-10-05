<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Models\Attendance;
use App\Models\Material;
use App\Models\MaterialTransaction;

// Redireksi Halaman Utama ke Dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// 1. Dashboard
Route::get('/dashboard', function () {
    return response()->json([
        'total_material' => Material::count(),
        'total_stock' => Material::sum('stok_akhir'),
        'critical_materials' => Material::whereIn('status', ['KRITIS', 'RENDAH'])->count(),
        'attendance_today' => Attendance::whereDate('date', today())->count(),
    ]);
});

// 2. Absensi
Route::get('/absensi', [AbsensiController::class, 'index']);
Route::post('/absensi/masuk', [AbsensiController::class, 'masuk']);
Route::post('/absensi/keluar', [AbsensiController::class, 'keluar']);

// 3. Input Material
Route::get('/material/input', function () {
    return response()->json(MaterialTransaction::with('material')
        ->where('type', 'IN')->latest()->get());
});

// 4. Output Material
Route::get('/material/output', function () {
    return response()->json(MaterialTransaction::with('material')
        ->where('type', 'OUT')->latest()->get());
});

// 5. Laporan Barang
Route::get('/laporan', function () {
    return response()->json([
        'total_transactions' => MaterialTransaction::count(),
        'incoming_transactions' => MaterialTransaction::where('type', 'IN')->count(),
        'outgoing_transactions' => MaterialTransaction::where('type', 'OUT')->count(),
        'materials' => Material::orderBy('nama_material')->get(),
    ]);
});

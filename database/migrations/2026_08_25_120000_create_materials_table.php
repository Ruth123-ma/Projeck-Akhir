<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->index();
            $table->string('nama_material');
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->decimal('stok_akhir', 12, 2)->default(0); // Dibatuhkan oleh WarehouseController
            $table->decimal('min_stok', 12, 2)->default(10);   // Batas peringatan stok kritis
            $table->string('satuan');
            $table->string('status')->default('NORMAL');       // 'NORMAL' atau 'KRITIS'
            $table->date('tanggal_transaksi');
            $table->string('jam_input');
            $table->string('bulan_simpanan')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
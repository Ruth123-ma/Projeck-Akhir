<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('material_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->index();
            $table->string('nama_material');
            $table->decimal('jumlah', 12, 2);
            $table->string('satuan', 20);
            $table->date('tanggal_transaksi');
            $table->time('jam_input');
            $table->string('bulan_simpanan', 20)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('material_keluar');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('material_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('nama_material');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->date('tanggal_transaksi');
            $table->time('jam_input');
            $table->string('bulan_simpanan');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('material_masuks');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat Tabel Materials terlebih dahulu (Tabel Induk)
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->string('kode_barang')->index();
                $table->string('nama_material');
                $table->decimal('jumlah', 12, 2)->default(0);
                $table->decimal('stok_akhir', 12, 2)->default(0);
                $table->decimal('min_stok', 12, 2)->default(10);
                $table->string('satuan');
                $table->string('status')->default('NORMAL');
                $table->date('tanggal_transaksi')->nullable();
                $table->string('jam_input')->nullable();
                $table->string('bulan_simpanan')->nullable()->index();
                $table->timestamps();
            });
        }

        // 2. Buat Tabel Attendances (Absensi)
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->string('employee_id')->index();
                $table->date('date')->index();
                $table->time('clock_in')->nullable();
                $table->time('clock_out')->nullable();
                $table->string('status')->default('ACTIVE');
                $table->timestamps();
            });
        }

        // 3. Buat Tabel Material Transactions (Wajib dibuat SESUDAH tabel materials)
        if (!Schema::hasTable('material_transactions')) {
            Schema::create('material_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('material_id')
                      ->constrained('materials')
                      ->onDelete('cascade');
                $table->enum('type', ['IN', 'OUT']);
                $table->decimal('quantity', 12, 2);
                $table->string('user_id')->nullable();
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_transactions');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('materials');
    }
};
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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->index(); // ID Karyawan (misal: LOG-OP-29481)
            $table->date('date')->index();          // Tanggal presensi (YYYY-MM-DD)
            $table->time('clock_in')->nullable();   // Jam masuk
            $table->time('clock_out')->nullable();  // Jam keluar
            $table->string('status')->default('ACTIVE'); // ACTIVE, LATE, ON-TIME
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
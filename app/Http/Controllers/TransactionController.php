<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_transactions', function (Blueprint $table) {
            $table->id();
            // Menghubungkan secara eksplisit ke tabel 'materials'
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->enum('type', ['IN', 'OUT']);
            $table->double('quantity');
            $table->string('user_id')->default('WH-OP-092');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_transactions');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_transactions', function (Blueprint $table) {
            $table->id();
            // Menautkan ID material secara eksplisit dengan penanganan cascading delete
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

    public function down(): void
    {
        Schema::dropIfExists('material_transactions');
    }
};
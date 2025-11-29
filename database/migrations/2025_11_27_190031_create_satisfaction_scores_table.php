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
        Schema::create('satisfaction_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained()->onDelete('cascade');
            $table->integer('quality_product'); // Mutu Produk
            $table->integer('spec_conformity'); // Kesesuaian Spesifikasi
            $table->integer('quality_consistency'); // Konsistensi Kualitas
            $table->integer('price_quality'); // Harga vs Kualitas
            $table->integer('product_condition'); // Kondisi Produk
            $table->integer('packaging_condition'); // Kondisi Kemasan
            $table->float('score');
            $table->date('period');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satisfaction_scores');
    }
};

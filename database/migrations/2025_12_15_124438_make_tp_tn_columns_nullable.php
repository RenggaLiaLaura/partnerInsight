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
        Schema::table('satisfaction_scores', function (Blueprint $table) {
            // Kualitas Produk
            $table->integer('mutu_produk_tp')->nullable()->change();
            $table->integer('mutu_produk_tn')->nullable()->change();
            $table->integer('kesesuaian_spesifikasi_tp')->nullable()->change();
            $table->integer('kesesuaian_spesifikasi_tn')->nullable()->change();
            $table->integer('konsistensi_kualitas_tp')->nullable()->change();
            $table->integer('konsistensi_kualitas_tn')->nullable()->change();
            $table->integer('harga_produk_tp')->nullable()->change();
            $table->integer('harga_produk_tn')->nullable()->change();
            $table->integer('kondisi_produk_tp')->nullable()->change();
            $table->integer('kondisi_produk_tn')->nullable()->change();
            $table->integer('kondisi_kemasan_tp')->nullable()->change();
            $table->integer('kondisi_kemasan_tn')->nullable()->change();

            // Service/Pelayanan
            $table->integer('ketersediaan_produk_tp')->nullable()->change();
            $table->integer('ketersediaan_produk_tn')->nullable()->change();
            $table->integer('kesesuaian_po_tp')->nullable()->change();
            $table->integer('kesesuaian_po_tn')->nullable()->change();
            $table->integer('info_kekosongan_tp')->nullable()->change();
            $table->integer('info_kekosongan_tn')->nullable()->change();
            $table->integer('ketepatan_waktu_tp')->nullable()->change();
            $table->integer('ketepatan_waktu_tn')->nullable()->change();
            $table->integer('info_pemberangkatan_tp')->nullable()->change();
            $table->integer('info_pemberangkatan_tn')->nullable()->change();
            $table->integer('kelengkapan_dokumen_tp')->nullable()->change();
            $table->integer('kelengkapan_dokumen_tn')->nullable()->change();
            $table->integer('kondisi_kendaraan_tp')->nullable()->change();
            $table->integer('kondisi_kendaraan_tn')->nullable()->change();
            $table->integer('sikap_sales_tp')->nullable()->change();
            $table->integer('sikap_sales_tn')->nullable()->change();
            $table->integer('kecakapan_sales_tp')->nullable()->change();
            $table->integer('kecakapan_sales_tn')->nullable()->change();
            $table->integer('kemudahan_komunikasi_tp')->nullable()->change();
            $table->integer('kemudahan_komunikasi_tn')->nullable()->change();
            $table->integer('respon_keluhan_tp')->nullable()->change();
            $table->integer('respon_keluhan_tn')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('satisfaction_scores', function (Blueprint $table) {
            // Revert to Not Null (assuming default was not null, though integer usually is)
            // Ideally we wouldn't revert to strictly not null if data might depend on it, 
            // but for symmetry:
             $table->integer('mutu_produk_tp')->nullable(false)->change();
             $table->integer('mutu_produk_tn')->nullable(false)->change();
        });
    }
};

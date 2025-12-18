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
            // Drop TP/TN columns
            $table->dropColumn([
                'tp1', 'tp2', 'tp3', 'tp4', 'tp5',
                'tn1', 'tn2', 'tn3', 'tn4', 'tn5'
            ]);

            // Brand new columns (Kualitas Produk)
            $table->integer('mutu_produk')->after('distributor_id');
            $table->integer('kesesuaian_spesifikasi')->after('mutu_produk');
            $table->integer('konsistensi_kualitas')->after('kesesuaian_spesifikasi');
            $table->integer('harga_produk')->after('konsistensi_kualitas');
            $table->integer('kondisi_produk')->after('harga_produk');
            $table->integer('kondisi_kemasan')->after('kondisi_produk');

            // Brand new columns (Service/Pelayanan)
            $table->integer('ketersediaan_produk')->after('kondisi_kemasan');
            $table->integer('kesesuaian_po')->after('ketersediaan_produk');
            $table->integer('info_kekosongan')->after('kesesuaian_po');
            $table->integer('ketepatan_waktu')->after('info_kekosongan');
            $table->integer('info_pemberangkatan')->after('ketepatan_waktu');
            $table->integer('kelengkapan_dokumen')->after('info_pemberangkatan');
            $table->integer('kondisi_kendaraan')->after('kelengkapan_dokumen');
            $table->integer('sikap_sales')->after('kondisi_kendaraan');
            $table->integer('kecakapan_sales')->after('sikap_sales');
            $table->integer('kemudahan_komunikasi')->after('kecakapan_sales');
            $table->integer('respon_keluhan')->after('kemudahan_komunikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('satisfaction_scores', function (Blueprint $table) {
             // Drop new columns
             $table->dropColumn([
                'mutu_produk', 'kesesuaian_spesifikasi', 'konsistensi_kualitas', 
                'harga_produk', 'kondisi_produk', 'kondisi_kemasan',
                'ketersediaan_produk', 'kesesuaian_po', 'info_kekosongan', 
                'ketepatan_waktu', 'info_pemberangkatan', 'kelengkapan_dokumen', 
                'kondisi_kendaraan', 'sikap_sales', 'kecakapan_sales', 
                'kemudahan_komunikasi', 'respon_keluhan'
            ]);

             // Restore TP/TN (simplified restore, data lost)
            $table->integer('tp1')->nullable();
            $table->integer('tp2')->nullable();
            $table->integer('tp3')->nullable();
            $table->integer('tp4')->nullable();
            $table->integer('tp5')->nullable();
            $table->integer('tn1')->nullable();
            $table->integer('tn2')->nullable();
            $table->integer('tn3')->nullable();
            $table->integer('tn4')->nullable();
            $table->integer('tn5')->nullable();
        });
    }
};

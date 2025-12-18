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
            // Drop simple columns
            $table->dropColumn([
                'mutu_produk', 'kesesuaian_spesifikasi', 'konsistensi_kualitas', 
                'harga_produk', 'kondisi_produk', 'kondisi_kemasan',
                'ketersediaan_produk', 'kesesuaian_po', 'info_kekosongan', 
                'ketepatan_waktu', 'info_pemberangkatan', 'kelengkapan_dokumen', 
                'kondisi_kendaraan', 'sikap_sales', 'kecakapan_sales', 
                'kemudahan_komunikasi', 'respon_keluhan'
            ]);

            // Add new TP/TN pairs (34 columns)
            // Kualitas Produk
            $table->integer('mutu_produk_tp')->after('distributor_id');
            $table->integer('mutu_produk_tn')->after('mutu_produk_tp');
            $table->integer('kesesuaian_spesifikasi_tp')->after('mutu_produk_tn');
            $table->integer('kesesuaian_spesifikasi_tn')->after('kesesuaian_spesifikasi_tp');
            $table->integer('konsistensi_kualitas_tp')->after('kesesuaian_spesifikasi_tn');
            $table->integer('konsistensi_kualitas_tn')->after('konsistensi_kualitas_tp');
            $table->integer('harga_produk_tp')->after('konsistensi_kualitas_tn');
            $table->integer('harga_produk_tn')->after('harga_produk_tp');
            $table->integer('kondisi_produk_tp')->after('harga_produk_tn');
            $table->integer('kondisi_produk_tn')->after('kondisi_produk_tp');
            $table->integer('kondisi_kemasan_tp')->after('kondisi_produk_tn');
            $table->integer('kondisi_kemasan_tn')->after('kondisi_kemasan_tp');

            // Service/Pelayanan
            $table->integer('ketersediaan_produk_tp')->after('kondisi_kemasan_tn');
            $table->integer('ketersediaan_produk_tn')->after('ketersediaan_produk_tp');
            $table->integer('kesesuaian_po_tp')->after('ketersediaan_produk_tn');
            $table->integer('kesesuaian_po_tn')->after('kesesuaian_po_tp');
            $table->integer('info_kekosongan_tp')->after('kesesuaian_po_tn');
            $table->integer('info_kekosongan_tn')->after('info_kekosongan_tp');
            $table->integer('ketepatan_waktu_tp')->after('info_kekosongan_tn');
            $table->integer('ketepatan_waktu_tn')->after('ketepatan_waktu_tp');
            $table->integer('info_pemberangkatan_tp')->after('ketepatan_waktu_tn');
            $table->integer('info_pemberangkatan_tn')->after('info_pemberangkatan_tp');
            $table->integer('kelengkapan_dokumen_tp')->after('info_pemberangkatan_tn');
            $table->integer('kelengkapan_dokumen_tn')->after('kelengkapan_dokumen_tp');
            $table->integer('kondisi_kendaraan_tp')->after('kelengkapan_dokumen_tn');
            $table->integer('kondisi_kendaraan_tn')->after('kondisi_kendaraan_tp');
            $table->integer('sikap_sales_tp')->after('kondisi_kendaraan_tn');
            $table->integer('sikap_sales_tn')->after('sikap_sales_tp');
            $table->integer('kecakapan_sales_tp')->after('sikap_sales_tn');
            $table->integer('kecakapan_sales_tn')->after('kecakapan_sales_tp');
            $table->integer('kemudahan_komunikasi_tp')->after('kecakapan_sales_tn');
            $table->integer('kemudahan_komunikasi_tn')->after('kemudahan_komunikasi_tp');
            $table->integer('respon_keluhan_tp')->after('kemudahan_komunikasi_tn');
            $table->integer('respon_keluhan_tn')->after('respon_keluhan_tp');
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
                'mutu_produk_tp', 'mutu_produk_tn',
                'kesesuaian_spesifikasi_tp', 'kesesuaian_spesifikasi_tn',
                'konsistensi_kualitas_tp', 'konsistensi_kualitas_tn',
                'harga_produk_tp', 'harga_produk_tn',
                'kondisi_produk_tp', 'kondisi_produk_tn',
                'kondisi_kemasan_tp', 'kondisi_kemasan_tn',
                'ketersediaan_produk_tp', 'ketersediaan_produk_tn',
                'kesesuaian_po_tp', 'kesesuaian_po_tn',
                'info_kekosongan_tp', 'info_kekosongan_tn',
                'ketepatan_waktu_tp', 'ketepatan_waktu_tn',
                'info_pemberangkatan_tp', 'info_pemberangkatan_tn',
                'kelengkapan_dokumen_tp', 'kelengkapan_dokumen_tn',
                'kondisi_kendaraan_tp', 'kondisi_kendaraan_tn',
                'sikap_sales_tp', 'sikap_sales_tn',
                'kecakapan_sales_tp', 'kecakapan_sales_tn',
                'kemudahan_komunikasi_tp', 'kemudahan_komunikasi_tn',
                'respon_keluhan_tp', 'respon_keluhan_tn'
            ]);

            // Restore simple columns (simplified)
            $table->integer('mutu_produk')->nullable();
            $table->integer('kesesuaian_spesifikasi')->nullable();
            $table->integer('konsistensi_kualitas')->nullable();
            $table->integer('harga_produk')->nullable();
            $table->integer('kondisi_produk')->nullable();
            $table->integer('kondisi_kemasan')->nullable();
            $table->integer('ketersediaan_produk')->nullable();
            $table->integer('kesesuaian_po')->nullable();
            $table->integer('info_kekosongan')->nullable();
            $table->integer('ketepatan_waktu')->nullable();
            $table->integer('info_pemberangkatan')->nullable();
            $table->integer('kelengkapan_dokumen')->nullable();
            $table->integer('kondisi_kendaraan')->nullable();
            $table->integer('sikap_sales')->nullable();
            $table->integer('kecakapan_sales')->nullable();
            $table->integer('kemudahan_komunikasi')->nullable();
            $table->integer('respon_keluhan')->nullable();
        });
    }
};

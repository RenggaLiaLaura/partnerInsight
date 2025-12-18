<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatisfactionScore extends Model
{
    protected $fillable = [
        'distributor_id',
        'distributor_id',
        // Kualitas Produk
        'mutu_produk_tp', 'mutu_produk_tn',
        'kesesuaian_spesifikasi_tp', 'kesesuaian_spesifikasi_tn',
        'konsistensi_kualitas_tp', 'konsistensi_kualitas_tn',
        'harga_produk_tp', 'harga_produk_tn',
        'kondisi_produk_tp', 'kondisi_produk_tn',
        'kondisi_kemasan_tp', 'kondisi_kemasan_tn',
        // Service/Pelayanan
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
        'respon_keluhan_tp', 'respon_keluhan_tn',
        'score',
        'period',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}

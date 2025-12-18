<?php

namespace App\Exports;

use App\Models\SatisfactionScore;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SatisfactionExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dimensions = [
        'mutu_produk', 'kesesuaian_spesifikasi', 'konsistensi_kualitas',
        'harga_produk', 'kondisi_produk', 'kondisi_kemasan',
        'ketersediaan_produk', 'kesesuaian_po', 'info_kekosongan',
        'ketepatan_waktu', 'info_pemberangkatan', 'kelengkapan_dokumen',
        'kondisi_kendaraan', 'sikap_sales', 'kecakapan_sales',
        'kemudahan_komunikasi', 'respon_keluhan'
    ];

    public function collection()
    {
        return SatisfactionScore::with('distributor')->orderBy('period', 'desc')->get();
    }

    public function headings(): array
    {
        $headers = [
            'Distributor Code',
            'Distributor Name',
            'Region',
            'Period',
        ];

        foreach ($this->dimensions as $dim) {
            $headers[] = strtoupper($dim) . ' (TP)';
            $headers[] = strtoupper($dim) . ' (TN)';
        }

        $headers[] = 'Final Score';

        return $headers;
    }

    public function map($satisfaction): array
    {
        $row = [
            $satisfaction->distributor->code ?? '',
            $satisfaction->distributor->name ?? '',
            $satisfaction->distributor->region ?? '',
            $satisfaction->period,
        ];

        foreach ($this->dimensions as $dim) {
            $row[] = $satisfaction->{$dim . '_tp'};
            $row[] = $satisfaction->{$dim . '_tn'};
        }

        $row[] = $satisfaction->score;

        return $row;
    }
}

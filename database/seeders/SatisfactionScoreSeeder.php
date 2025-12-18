<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SatisfactionScore;
use App\Models\Distributor;
use Carbon\Carbon;

class SatisfactionScoreSeeder extends Seeder
{
    public function run()
    {
        $distributors = Distributor::all();
        
        $fields = [
            'mutu_produk', 'kesesuaian_spesifikasi', 'konsistensi_kualitas',
            'harga_produk', 'kondisi_produk', 'kondisi_kemasan',
            'ketersediaan_produk', 'kesesuaian_po', 'info_kekosongan',
            'ketepatan_waktu', 'info_pemberangkatan', 'kelengkapan_dokumen',
            'kondisi_kendaraan', 'sikap_sales', 'kecakapan_sales',
            'kemudahan_komunikasi', 'respon_keluhan'
        ];

        foreach ($distributors as $distributor) {
            // Generate data for each month of 2025
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create(2025, $month, 1);
                
                $data = [
                    'distributor_id' => $distributor->id,
                    'period' => $date->format('Y-m-d'),
                ];
                
                $totalScore = 0;
                
                foreach ($fields as $field) {
                    // Randomly decide if entry is TP (positive) or TN (negative)
                    // High probability of TP (3-5) for realistic data
                    $isTp = mt_rand(0, 10) > 1; // 90% chance of TP
                    
                    if ($isTp) {
                        $val = mt_rand(3, 5);
                        $data[$field . '_tp'] = $val;
                        $data[$field . '_tn'] = null;
                        $totalScore += $val;
                    } else {
                        // If TN, 1 is 'Very Low Negative' (Good), 5 is 'Very High Negative' (Bad)
                        // Score calculation: 6 - TN
                        $val = mt_rand(1, 2); // Low negative impact (mostly good)
                        $data[$field . '_tp'] = null;
                        $data[$field . '_tn'] = $val;
                        $totalScore += (6 - $val);
                    }
                }

                $data['score'] = round($totalScore / count($fields), 2);

                SatisfactionScore::create($data);
            }
        }
    }
}

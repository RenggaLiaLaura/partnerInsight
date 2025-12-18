<?php

namespace App\Imports;

use App\Models\SatisfactionScore;
use App\Models\Distributor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class SatisfactionImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable;

    protected $errors = [];
    protected $failures = [];

    // List of 17 dimensions
    protected $dimensions = [
        'mutu_produk', 'kesesuaian_spesifikasi', 'konsistensi_kualitas',
        'harga_produk', 'kondisi_produk', 'kondisi_kemasan',
        'ketersediaan_produk', 'kesesuaian_po', 'info_kekosongan',
        'ketepatan_waktu', 'info_pemberangkatan', 'kelengkapan_dokumen',
        'kondisi_kendaraan', 'sikap_sales', 'kecakapan_sales',
        'kemudahan_komunikasi', 'respon_keluhan'
    ];

    public function model(array $row)
    {
        // Fallback to name if code is missing, but prefer code
        $distributor = null;
        if (!empty($row['distributor_code'])) {
            $distributor = Distributor::where('code', $row['distributor_code'])->first();
        } elseif (!empty($row['distributor_name'])) {
             $distributor = Distributor::where('name', $row['distributor_name'])->first();
        }

        if (!$distributor) {
            $identifier = $row['distributor_code'] ?? $row['distributor_name'] ?? 'Unknown';
            throw new \Exception("Distributor '$identifier' not found");
        }

        $data = [
            'distributor_id' => $distributor->id,
            'period'         => $row['period'],
        ];

        $totalScore = 0;

        foreach ($this->dimensions as $field) {
            // Excel headings are usually snake_case or we sanitize them
            // We expect columns like "mutu_produk_tp", "mutu_produk_tn"
            $tpKey = $field . '_tp';
            $tnKey = $field . '_tn';

            $tpVal = isset($row[$tpKey]) ? $row[$tpKey] : null;
            $tnVal = isset($row[$tnKey]) ? $row[$tnKey] : null;

            $data[$tpKey] = $tpVal;
            $data[$tnKey] = $tnVal;

            // Score logic
            if ($tpVal) {
                $totalScore += $tpVal;
            } elseif ($tnVal) {
                $totalScore += (6 - $tnVal);
            }
            // If both null, score adds 0 (or should we validate?)
        }

        $data['score'] = round($totalScore / count($this->dimensions), 2);

        // Update or create based on distributor and period? 
        // For import, usually we create new or update existing.
        // Let's use updateOrCreate to prevent duplicates for same period
        return SatisfactionScore::updateOrCreate(
            ['distributor_id' => $data['distributor_id'], 'period' => $data['period']],
            $data
        );
    }

    public function rules(): array
    {
        $rules = [
            'period' => 'required|date_format:Y-m-d',
        ];

        // We require either code or name
        // Custom validation logic might be needed, but for now simple rules
        
        foreach ($this->dimensions as $field) {
            $rules[$field . '_tp'] = 'nullable|integer|min:1|max:5';
            $rules[$field . '_tn'] = 'nullable|integer|min:1|max:5';
        }

        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'period.required' => 'Period is required',
            'period.date_format' => 'Period must be YYYY-MM-DD',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFailures()
    {
        return $this->failures;
    }
}

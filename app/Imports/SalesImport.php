<?php

namespace App\Imports;

use App\Models\Sales;
use App\Models\Distributor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class SalesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable;

    protected $errors = [];
    protected $failures = [];

    public function model(array $row)
    {
        $distributor = Distributor::where('name', $row['distributor_name'])->first();
        
        if (!$distributor) {
            throw new \Exception("Distributor '{$row['distributor_name']}' not found");
        }

        return new Sales([
            'distributor_id' => $distributor->id,
            'amount' => $row['amount'],
            'period' => $row['period'],
        ]);
    }

    public function rules(): array
    {
        return [
            'distributor_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'period' => 'required|date_format:Y-m-d',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'distributor_name.required' => 'Distributor name is required',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'period.required' => 'Period is required',
            'period.date_format' => 'Period must be in format YYYY-MM-DD',
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

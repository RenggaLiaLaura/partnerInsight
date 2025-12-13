<?php

namespace App\Imports;

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

class DistributorImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithMultipleSheets
{
    use Importable;

    protected $errors = [];
    protected $failures = [];

    public function model(array $row)
    {
        // Normalize keys to lowercase to handle both "Name" and "name"
        $row = array_change_key_case($row, CASE_LOWER);
        
        return new Distributor([
            'name'    => $row['name'] ?? null,
            'region'  => $row['region'] ?? null,
            'address' => $row['address'] ?? null,
            'phone'   => $row['phone'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Distributor name is required',
            'region.required' => 'Region is required',
            'address.required' => 'Address is required',
            'phone.required' => 'Phone number is required',
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

    /**
     * Define which sheet(s) should be processed.
     * Only the sheet named "Distributors" will be imported.
     */
    public function sheets(): array
    {
        return [
            0 => $this, // import the first sheet regardless of its name
        ];
    }
}

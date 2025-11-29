<?php

namespace App\Imports;

use App\Models\Satisfaction;
use App\Models\Distributor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class SatisfactionImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
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

        return new Satisfaction([
            'distributor_id' => $distributor->id,
            'score' => $row['score'],
            'period' => $row['period'],
        ]);
    }

    public function rules(): array
    {
        return [
            'distributor_name' => 'required|string',
            'score' => 'required|numeric|min:0|max:5',
            'period' => 'required|date_format:Y-m-d',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'distributor_name.required' => 'Distributor name is required',
            'score.required' => 'Score is required',
            'score.numeric' => 'Score must be a number',
            'score.min' => 'Score must be at least 0',
            'score.max' => 'Score must not exceed 5',
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

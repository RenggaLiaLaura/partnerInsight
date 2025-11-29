<?php

namespace App\Exports;

use App\Models\Distributor;
use App\Models\SalesPerformance;
use App\Models\SatisfactionScore;
use App\Models\ClusteringResult;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllDataExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Distributors' => new DistributorsSheet(),
            'Sales' => new SalesSheet(),
            'Satisfaction' => new SatisfactionSheet(),
            'Clustering Results' => new \App\Exports\ClusteringExport(),
        ];
    }
}

// Distributors Sheet
class DistributorsSheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    public function collection()
    {
        return Distributor::select('name', 'region', 'address', 'phone')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Region', 'Address', 'Phone'];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 20, 'C' => 40, 'D' => 20];
    }
}

// Sales Sheet
class SalesSheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths
{
    public function collection()
    {
        return SalesPerformance::with('distributor')->get();
    }

    public function map($sale): array
    {
        return [
            $sale->distributor->name,
            'Rp ' . number_format($sale->amount, 0, ',', '.'),
            date('F Y', strtotime($sale->period)),
        ];
    }

    public function headings(): array
    {
        return ['Distributor', 'Amount', 'Period'];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 25, 'C' => 20];
    }
}

// Satisfaction Sheet
class SatisfactionSheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths
{
    public function collection()
    {
        return SatisfactionScore::with('distributor')->get();
    }

    public function map($satisfaction): array
    {
        return [
            $satisfaction->distributor->name,
            number_format($satisfaction->score, 1),
            date('F Y', strtotime($satisfaction->period)),
        ];
    }

    public function headings(): array
    {
        return ['Distributor', 'Score', 'Period'];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 15, 'C' => 20];
    }
}

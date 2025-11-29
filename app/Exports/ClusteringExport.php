<?php

namespace App\Exports;

use App\Models\ClusteringResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ClusteringExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return ClusteringResult::with('distributor')->get();
    }

    public function headings(): array
    {
        return [
            'Distributor',
            'Satisfaction',
            'Quantity (Carton)',
            'Cluster'
        ];
    }

    public function map($result): array
    {
        return [
            $result->distributor->name,
            number_format($result->score_satisfaction, 0),
            number_format($result->score_sales, 0, ',', '.'),
            $result->cluster_group
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Distributor
            'B' => 15,  // Satisfaction
            'C' => 25,  // Sales Score
            'D' => 20,  // Assigned Cluster
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DistributorImport;
use App\Exports\DistributorTemplateExport;
use App\Exports\AllDataExport;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        $query = Distributor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%");
        }

        $distributors = $query->paginate(10);
        return view('distributors.index', compact('distributors'));
    }

    public function create()
    {
        return view('distributors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric',
            'region' => 'required|string',
            'address' => 'required|string',
        ]);

        Distributor::create($request->all());

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor created successfully.');
    }

    public function show(Distributor $distributor)
    {
        $distributor->load(['satisfactionScores' => function($query) {
            $query->orderBy('period', 'asc');
        }, 'salesPerformances' => function($query) {
            $query->orderBy('period', 'asc');
        }, 'clusteringResult']);

        // Prepare Chart Data
        $satisfactionData = [
            'labels' => $distributor->satisfactionScores->pluck('period')->map(function($date) {
                return date('M Y', strtotime($date));
            }),
            'data' => $distributor->satisfactionScores->pluck('score'),
        ];

        $salesData = [
            'labels' => $distributor->salesPerformances->pluck('period')->map(function($date) {
                return date('M Y', strtotime($date));
            }),
            'data' => $distributor->salesPerformances->pluck('amount'),
        ];

        return view('distributors.show', compact('distributor', 'satisfactionData', 'salesData'));
    }

    public function edit(Distributor $distributor)
    {
        return view('distributors.edit', compact('distributor'));
    }

    public function update(Request $request, Distributor $distributor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric',
            'region' => 'required|string',
            'address' => 'required|string',
        ]);

        $distributor->update($request->all());

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor updated successfully');
    }

    public function destroy(Distributor $distributor)
    {
        $distributor->delete();

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor deleted successfully');
    }

    public function showImportForm()
    {
        return view('distributors.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $import = new DistributorImport();
        Excel::import($import, $request->file('file'));

        $errors = $import->getErrors();
        $failures = $import->getFailures();

        if (count($errors) > 0 || count($failures) > 0) {
            $errorMessages = array_merge(
                $errors,
                array_map(function($failure) {
                    return "Row {$failure->row()}: " . implode(', ', $failure->errors());
                }, $failures)
            );

            return redirect()->route('distributors.import')
                ->with('errors_count', count($errorMessages))
                ->with('import_errors', $errorMessages);
        }

        return redirect()->route('distributors.index')
            ->with('success', 'Distributors imported successfully!');
    }

    public function downloadTemplate()
    {
        return Excel::download(new DistributorTemplateExport, 'distributor_template.xlsx');
    }

    public function exportAll()
    {
        return Excel::download(new AllDataExport, 'all_data_' . date('Y-m-d') . '.xlsx');
    }
}

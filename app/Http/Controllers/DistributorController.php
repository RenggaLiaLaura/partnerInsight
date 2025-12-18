<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DistributorImport;
use App\Exports\DistributorTemplateExport;
use App\Exports\AllDataExport;
use Illuminate\Support\Facades\Auth;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        $query = Distributor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $distributors = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('distributors.partials.table', compact('distributors'));
        }

        return view('distributors.index', compact('distributors'));
    }

    public function create()
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        return view('distributors.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $request->validate([
            'code' => 'required|string|unique:distributors|max:50',
            'name' => 'required|string|max:255|unique:distributors',
            'phone' => 'required|numeric',
            'address' => 'required|string',
            'province_id' => 'required|string',
            'regency_id' => 'required|string',
            'district_id' => 'required|string',
            'village_id' => 'required|string',
        ]);

        $data = $request->all();
        // Concatenate region names for the 'region' column
        $data['region'] = implode(', ', [
            $request->province_name,
            $request->regency_name,
            $request->district_name,
            $request->village_name
        ]);

        Distributor::create($data);

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
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        return view('distributors.edit', compact('distributor'));
    }

    public function update(Request $request, Distributor $distributor)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $request->validate([
            'code' => 'required|string|max:50|unique:distributors,code,' . $distributor->id,
            'name' => 'required|string|max:255|unique:distributors,name,' . $distributor->id,
            'phone' => 'required|numeric',
            'address' => 'required|string',
            'province_id' => 'required|string',
            'regency_id' => 'required|string',
            'district_id' => 'required|string',
            'village_id' => 'required|string',
        ]);

        $data = $request->all();
        // Concatenate region names for the 'region' column
        $data['region'] = implode(', ', [
            $request->province_name,
            $request->regency_name,
            $request->district_name,
            $request->village_name
        ]);

        $distributor->update($data);

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor updated successfully');
    }

    public function destroy(Distributor $distributor)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $distributor->delete();

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor deleted successfully');
    }

    public function showImportForm()
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        return view('distributors.import');
    }

    public function import(Request $request)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
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
        if (Auth::user()->role === 'staff') {
            abort(403, 'Unauthorized action. Staff cannot export data.');
        }
        return Excel::download(new AllDataExport, 'all_data_' . date('Y-m-d') . '.xlsx');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SalesPerformance;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalesImport;
use App\Exports\SalesTemplateExport;
use App\Exports\AllDataExport;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = Distributor::with(['salesPerformances' => function($q) {
            $q->orderBy('period', 'desc');
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $distributors = $query->paginate(10);

        if ($request->ajax()) {
            return view('sales.partials.table', compact('distributors'))->render();
        }

        return view('sales.index', compact('distributors'));
    }

    public function create()
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $distributors = Distributor::all();
        return view('sales.create', compact('distributors'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $request->validate([
            'distributor_id' => 'required',
            'amount' => 'required|numeric|min:0',
            'period' => 'required|date',
        ]);

        SalesPerformance::create($request->all());

        return redirect()->route('sales.index')
            ->with('success', 'Sales Performance created successfully.');
    }

    public function edit(SalesPerformance $sale)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $distributors = Distributor::all();
        return view('sales.edit', compact('sale', 'distributors'));
    }

    public function update(Request $request, SalesPerformance $sale)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $request->validate([
            'distributor_id' => 'required',
            'amount' => 'required|numeric|min:0',
            'period' => 'required|date',
        ]);

        $sale->update($request->all());

        return redirect()->route('sales.index')
            ->with('success', 'Sales Performance updated successfully');
    }

    public function destroy(SalesPerformance $sale)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Sales Performance deleted successfully');
    }

    public function showImportForm()
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        return view('sales.import');
    }

    public function import(Request $request)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $import = new SalesImport();
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

            return redirect()->route('sales.import')
                ->with('errors_count', count($errorMessages))
                ->with('import_errors', $errorMessages);
        }

        return redirect()->route('sales.index')
            ->with('success', 'Sales data imported successfully!');
    }

    public function downloadTemplate()
    {
        return Excel::download(new SalesTemplateExport, 'sales_template.xlsx');
    }

    public function exportAll()
    {
        if (Auth::user()->role === 'staff') {
            abort(403, 'Unauthorized action. Staff cannot export data.');
        }
        return Excel::download(new AllDataExport, 'all_data_' . date('Y-m-d') . '.xlsx');
    }
}

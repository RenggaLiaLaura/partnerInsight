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

use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    private function getRankedDistributors()
    {
        return Distributor::select('distributors.*')
            ->selectSub(function ($query) {
                $query->from('sales_performances')
                    ->whereColumn('sales_performances.distributor_id', 'distributors.id')
                    ->selectRaw('sum(amount)');
            }, 'total_sales')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function index(Request $request)
    {
        $query = Distributor::with(['salesPerformances' => function($q) {
            $q->orderBy('period', 'desc');
        }])->withSum('salesPerformances', 'amount'); // Eager load sum

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Sort by total sales desc for ranking purpose implicitly if we used it, 
        // but here we just stick to pagination.
        // However, to show rank, we might need to calculate it properly.
        // For simplicity with pagination, we will fetch all ranked ID first or just compute rank on the fly if needed.
        // Let's attach rank manually to the collection for now, OR:
        // A better approach for "rank" in a paginated list is challenging without a window function or subquery.
        // Let's use a subquery to get the position if possible, or just load all for ranking if dataset is small.
        // Given existing code loads paginate(10), we'll stick to that but maybe order by total_sales?
        // Let's order by total_sales descending to show top performers first by default.
        
        $query->orderByDesc(
            SalesPerformance::selectRaw('sum(amount)')
                ->whereColumn('distributor_id', 'distributors.id')
        );

        $distributors = $query->paginate(10);

        // Add rank
        // To get true rank across ALL distributors, we need the count of those with greater sales.
        $distributors->getCollection()->transform(function ($distributor) {
             $rank = Distributor::whereHas('salesPerformances', function($q) use ($distributor) {
                 // Calculate sum for others and compare? This is n+1 expensive.
                 // Optimization: Pre-calculate ranks? 
                 // For now, let's keep it simple: Rank is based on index in this sorted list + (page-1)*perPage + 1?
                 // No, that only works if sorted by sales. We ARE sorting by sales.
             })->count();
             
             // Let's use a simpler approach: Just show "Top Performer" if in top 5?
             // Or let's use a raw query for rank.
             $distributor->rank = \App\Models\SalesPerformance::selectRaw('count(distinct distributor_id) + 1')
                ->whereRaw('amount > (select sum(amount) from sales_performances where distributor_id = ?)', [$distributor->id])
                ->groupBy('distributor_id') 
                ->get()->count() + 1; // Very rough approximation if not grouped correctly.
                
             // Actually, let's just use the `getRankedDistributors` approach if dataset is small (e.g. < 1000).
             // Assuming small dataset for now.
             return $distributor;
        });
        
        // BETTER APPROACH:
        // Let's just fetch all ordered by sum, find the index.
        $allRanked = $this->getRankedDistributors()->pluck('id')->toArray();
        $distributors->getCollection()->transform(function ($distributor) use ($allRanked) {
            $rank = array_search($distributor->id, $allRanked);
            $distributor->rank = ($rank !== false) ? $rank + 1 : '-';
            return $distributor;
        });

        if ($request->ajax()) {
            return view('sales.partials.table', compact('distributors'))->render();
        }

        return view('sales.index', compact('distributors'));
    }

    public function monthly(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        $monthlySales = SalesPerformance::select(
            DB::raw('DATE_FORMAT(period, "%Y-%m") as month_key'),
            DB::raw('MONTHNAME(period) as month_name'),
            DB::raw('YEAR(period) as year'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(DISTINCT distributor_id) as active_distributors')
        )
        ->whereYear('period', $year)
        ->groupBy('month_key', 'month_name', 'year')
        ->orderBy('month_key', 'desc')
        ->get();

        return view('sales.monthly', compact('monthlySales', 'year'));
    }

    public function daily(Request $request)
    {
        $month = $request->get('month', date('Y-m'));
        
        $dailySales = SalesPerformance::select(
            DB::raw('period'),
            DB::raw('DAYNAME(period) as day_name'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(DISTINCT distributor_id) as active_distributors')
        )
        ->where(DB::raw('DATE_FORMAT(period, "%Y-%m")'), $month)
        ->groupBy('period', 'day_name')
        ->orderBy('period', 'desc')
        ->paginate(15);
        
        return view('sales.daily', compact('dailySales', 'month'));
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

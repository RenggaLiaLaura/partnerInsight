<?php

namespace App\Http\Controllers;

use App\Models\SalesPerformance;
use App\Models\Distributor;
use Illuminate\Http\Request;

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
        return view('sales.index', compact('distributors'));
    }

    public function create()
    {
        $distributors = Distributor::all();
        return view('sales.create', compact('distributors'));
    }

    public function store(Request $request)
    {
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
        $distributors = Distributor::all();
        return view('sales.edit', compact('sale', 'distributors'));
    }

    public function update(Request $request, SalesPerformance $sale)
    {
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
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Sales Performance deleted successfully');
    }
}

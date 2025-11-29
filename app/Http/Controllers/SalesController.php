<?php

namespace App\Http\Controllers;

use App\Models\SalesPerformance;
use App\Models\Distributor;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesPerformance::with('distributor');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('distributor', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $sales = $query->paginate(10);
        return view('sales.index', compact('sales'));
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

<?php

namespace App\Http\Controllers;

use App\Models\SatisfactionScore;
use App\Models\Distributor;
use Illuminate\Http\Request;

class SatisfactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Distributor::with(['satisfactionScores' => function($q) {
            $q->orderBy('period', 'desc');
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $distributors = $query->paginate(10);

        if ($request->ajax()) {
            return view('satisfaction.partials.table', compact('distributors'))->render();
        }

        return view('satisfaction.index', compact('distributors'));
    }

    public function create()
    {
        $distributors = Distributor::all();
        return view('satisfaction.create', compact('distributors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required',
            'quality_product' => 'required|integer|min:1|max:5',
            'spec_conformity' => 'required|integer|min:1|max:5',
            'quality_consistency' => 'required|integer|min:1|max:5',
            'price_quality' => 'required|integer|min:1|max:5',
            'product_condition' => 'required|integer|min:1|max:5',
            'packaging_condition' => 'required|integer|min:1|max:5',
            'period' => 'required|date',
        ]);

        $data = $request->all();
        
        // Calculate average score
        $total = $request->quality_product + 
                 $request->spec_conformity + 
                 $request->quality_consistency + 
                 $request->price_quality + 
                 $request->product_condition + 
                 $request->packaging_condition;
                 
        $data['score'] = round($total / 6, 2);

        SatisfactionScore::create($data);

        return redirect()->route('satisfaction.index')
            ->with('success', 'Satisfaction Score created successfully.');
    }

    public function edit(SatisfactionScore $satisfaction)
    {
        $distributors = Distributor::all();
        return view('satisfaction.edit', compact('satisfaction', 'distributors'));
    }

    public function update(Request $request, SatisfactionScore $satisfaction)
    {
        $request->validate([
            'distributor_id' => 'required',
            'quality_product' => 'required|integer|min:1|max:5',
            'spec_conformity' => 'required|integer|min:1|max:5',
            'quality_consistency' => 'required|integer|min:1|max:5',
            'price_quality' => 'required|integer|min:1|max:5',
            'product_condition' => 'required|integer|min:1|max:5',
            'packaging_condition' => 'required|integer|min:1|max:5',
            'period' => 'required|date',
        ]);

        $data = $request->all();

        // Calculate average score
        $total = $request->quality_product + 
                 $request->spec_conformity + 
                 $request->quality_consistency + 
                 $request->price_quality + 
                 $request->product_condition + 
                 $request->packaging_condition;
                 
        $data['score'] = round($total / 6, 2);

        $satisfaction->update($data);

        return redirect()->route('satisfaction.index')
            ->with('success', 'Satisfaction Score updated successfully');
    }

    public function destroy(SatisfactionScore $satisfaction)
    {
        $satisfaction->delete();

        return redirect()->route('satisfaction.index')
            ->with('success', 'Satisfaction Score deleted successfully');
    }
}

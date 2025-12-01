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
            'score' => 'required|numeric|min:0|max:5',
            'period' => 'required|date',
        ]);

        SatisfactionScore::create($request->all());

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
            'score' => 'required|numeric|min:0|max:5',
            'period' => 'required|date',
        ]);

        $satisfaction->update($request->all());

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

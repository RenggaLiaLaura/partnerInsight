<?php

namespace App\Http\Controllers;

use App\Models\SatisfactionScore;
use App\Models\Distributor;
use Illuminate\Http\Request;

class SatisfactionController extends Controller
{
    public function index(Request $request)
    {
        $query = SatisfactionScore::with('distributor');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('distributor', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $scores = $query->paginate(10);
        return view('satisfaction.index', compact('scores'));
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

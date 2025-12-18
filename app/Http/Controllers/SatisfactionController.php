<?php

namespace App\Http\Controllers;

use App\Models\SatisfactionScore;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $distributors = Distributor::all();
        return view('satisfaction.create', compact('distributors'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        // Define fields
        $fields = [
            'mutu_produk', 'kesesuaian_spesifikasi', 'konsistensi_kualitas',
            'harga_produk', 'kondisi_produk', 'kondisi_kemasan',
            'ketersediaan_produk', 'kesesuaian_po', 'info_kekosongan',
            'ketepatan_waktu', 'info_pemberangkatan', 'kelengkapan_dokumen',
            'kondisi_kendaraan', 'sikap_sales', 'kecakapan_sales',
            'kemudahan_komunikasi', 'respon_keluhan'
        ];

        // Build validation rules
        $rules = [
            'distributor_id' => 'required',
            'period' => 'required|date',
        ];

        // Allow nullable but value must be 1-5 if present
        foreach ($fields as $field) {
            $rules[$field . '_tp'] = 'nullable|integer|min:1|max:5';
            $rules[$field . '_tn'] = 'nullable|integer|min:1|max:5';
        }

        $request->validate($rules);

        // Additional validation: ensure at least one is filled for each field
        foreach ($fields as $field) {
            if (!$request->filled($field . '_tp') && !$request->filled($field . '_tn')) {
                return back()->withErrors([$field . '_tp' => "Either TP or TN must be filled for $field"])->withInput();
            }
        }

        $data = $request->all();
        
        // Calculate average score
        $totalScore = 0;
        foreach ($fields as $field) {
            if ($request->filled($field . '_tp')) {
                // TP is direct score (1-5)
                $totalScore += $request->input($field . '_tp');
            } elseif ($request->filled($field . '_tn')) {
                // TN is inverse score
                // 5 (Sangat Tinggi Negatif) -> Score 1 (Bad)
                // 1 (Sangat Rendah Negatif) -> Score 5 (Good)
                $tnValue = $request->input($field . '_tn');
                $totalScore += (6 - $tnValue);
            }
        }
                 
        $data['score'] = round($totalScore / 17, 2);

        SatisfactionScore::create($data);

        return redirect()->route('satisfaction.index')
            ->with('success', 'Satisfaction Score created successfully.');
    }

    public function edit(SatisfactionScore $satisfaction)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $distributors = Distributor::all();
        return view('satisfaction.edit', compact('satisfaction', 'distributors'));
    }

    public function update(Request $request, SatisfactionScore $satisfaction)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        // Define fields
        $fields = [
            'mutu_produk', 'kesesuaian_spesifikasi', 'konsistensi_kualitas',
            'harga_produk', 'kondisi_produk', 'kondisi_kemasan',
            'ketersediaan_produk', 'kesesuaian_po', 'info_kekosongan',
            'ketepatan_waktu', 'info_pemberangkatan', 'kelengkapan_dokumen',
            'kondisi_kendaraan', 'sikap_sales', 'kecakapan_sales',
            'kemudahan_komunikasi', 'respon_keluhan'
        ];

        // Build validation rules
        $rules = [
            'distributor_id' => 'required',
            'period' => 'required|date',
        ];

        // Allow nullable but value must be 1-5 if present
        foreach ($fields as $field) {
            $rules[$field . '_tp'] = 'nullable|integer|min:1|max:5';
            $rules[$field . '_tn'] = 'nullable|integer|min:1|max:5';
        }

        $request->validate($rules);

        // Additional validation: ensure at least one is filled for each field
        foreach ($fields as $field) {
            if (!$request->filled($field . '_tp') && !$request->filled($field . '_tn')) {
                return back()->withErrors([$field . '_tp' => "Either TP or TN must be filled for $field"])->withInput();
            }
        }

        $data = $request->all();
        
        // Calculate average score
        $totalScore = 0;
        foreach ($fields as $field) {
            if ($request->filled($field . '_tp')) {
                // TP is direct score (1-5)
                $totalScore += $request->input($field . '_tp');
            } elseif ($request->filled($field . '_tn')) {
                // TN is inverse score
                // 5 (Sangat Tinggi Negatif) -> Score 1 (Bad)
                // 1 (Sangat Rendah Negatif) -> Score 5 (Good)
                $tnValue = $request->input($field . '_tn');
                $totalScore += (6 - $tnValue);
            } else {
                 // Should be caught by validation, but handle strictly
                 // If for some reason both empty (due to update weirdness), treat as 0 or ignore?
                 // Validation above ensures one is present.
            }
        }
                 
        $data['score'] = round($totalScore / 17, 2);

        $satisfaction->update($data);

        return redirect()->route('satisfaction.index')
            ->with('success', 'Satisfaction Score updated successfully');
    }

    public function destroy(SatisfactionScore $satisfaction)
    {
        if (Auth::user()->role === 'manager') {
            abort(403, 'Unauthorized action. Managers have view-only access.');
        }
        $satisfaction->delete();

        return redirect()->route('satisfaction.index')
            ->with('success', 'Satisfaction Score deleted successfully');
    }
}

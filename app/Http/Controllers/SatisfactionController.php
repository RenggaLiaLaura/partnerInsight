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
        $year = $request->input('year', date('Y'));

        $query = Distributor::with(['satisfactionScores' => function($q) {
            $q->orderBy('period', 'desc');
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Get paginated results for the table
        $distributors = $query->clone()->paginate(10); // Clone to avoid modifying the query for stats

        // Calculate stats for Charts (Use all matching data, not just paginated)
        // We need all satisfaction scores from the filtered distributions
        $allDistributors = $query->get();
        // Base score query
        $scoreQuery = SatisfactionScore::whereIn('distributor_id', $allDistributors->pluck('id'))
                        ->whereYear('period', $year);
        
        // Filter by Month if searching/filtering
        if ($request->filled('month')) {
            $scoreQuery->whereMonth('period', $request->month);
        }

        $allScores = $scoreQuery->get();

        // reuse helper logic (copy-paste for now or refactor to private method if I can access it easily)
        $productCriteria = [
            'mutu_produk' => 'Kualitas Produk',
            'kesesuaian_spesifikasi' => 'Kesesuaian Spesifikasi',
            'konsistensi_kualitas' => 'Konsistensi', 
            'harga_produk' => 'Harga', 
            'kondisi_produk' => 'Kondisi Produk saat diterima',
            'kondisi_kemasan' => 'Kondisi kemasan saat diterima'
        ];

        $serviceCriteria = [
            'ketersediaan_produk' => 'Ketersediaan Produk',
            'kesesuaian_po' => 'Kesesuaian PO',
            'ketepatan_waktu' => 'Pengiriman Tepat Waktu',
            'info_kekosongan' => 'Info Kekosongan',
            'kelengkapan_dokumen' => 'Kelengkapan Dokumen',
            'kondisi_kendaraan' => 'Kondisi Kendaraan',
            'sikap_sales' => 'Sikap Sopan & Ramah',
            'kemudahan_komunikasi' => 'Kemudahan Berkomunikasi',
            'respon_keluhan' => 'Respon terhadap keluhan'
        ];

        $productStats = $this->calculateGroupStats($allScores, $productCriteria);
        $serviceStats = $this->calculateGroupStats($allScores, $serviceCriteria);
        
        // Generate Summary
        $summary = $this->generateSummary($productStats, $serviceStats, $year);

        // Get available years for filter
        // Assuming we want a dynamic range or fixed. Let's do dynamic from DB + current year
        $years = SatisfactionScore::selectRaw('YEAR(period) as year')
                    ->distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year')
                    ->toArray();
        
        if (!in_array(date('Y'), $years)) {
             array_unshift($years, date('Y'));
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('satisfaction.partials.table', compact('distributors'))->render(),
                'productStats' => $productStats,
                'serviceStats' => $serviceStats,
                'summary' => $summary
            ]);
        }

        return view('satisfaction.index', compact('distributors', 'productStats', 'serviceStats', 'summary', 'years', 'year'));
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

    public function generateReport(Request $request)
    {
        // 1. Gather Data
        $year = $request->input('year', date('Y'));
        $month = $request->input('month');

        // Start with Distributor query to handle search filtering
        $distributorQuery = Distributor::query();
        if ($request->filled('search')) {
            $distributorQuery->where('name', 'like', "%{$request->search}%");
        }
        $distributorIds = $distributorQuery->pluck('id');

        // Fetch scores for filtered distributors and year
        $query = SatisfactionScore::with('distributor') // Eager load distributor for detailed list
                    ->whereIn('distributor_id', $distributorIds)
                    ->whereYear('period', $year);

        if ($month) {
            $query->whereMonth('period', $month);
        }

        $scores = $query->get();

        if ($scores->isEmpty()) {
            return redirect()->back()->with('error', 'No data found for the selected criteria.');
        }

        // 2. Define Criteria Groups
        $productCriteria = [
            'mutu_produk' => 'Kualitas Produk',
            'kesesuaian_spesifikasi' => 'Kesesuaian Spesifikasi',
            'konsistensi_kualitas' => 'Konsistensi', 
            'harga_produk' => 'Harga', // Adjusted label to fit chart
            'kondisi_produk' => 'Kondisi Produk saat diterima',
            'kondisi_kemasan' => 'Kondisi kemasan saat diterima'
        ];

        $serviceCriteria = [
            'ketersediaan_produk' => 'Ketersediaan Produk',
            'kesesuaian_po' => 'Kesesuaian PO',
            'ketepatan_waktu' => 'Pengiriman Tepat Waktu', // Note: mapped somewhat to image labels
            'info_kekosongan' => 'Info Kekosongan', 
            'kelengkapan_dokumen' => 'Kelengkapan Dokumen',
            'kondisi_kendaraan' => 'Kondisi Kendaraan',
            'sikap_sales' => 'Sikap Sopan & Ramah',
            'kemudahan_komunikasi' => 'Kemudahan Berkomunikasi',
            'respon_keluhan' => 'Respon terhadap keluhan'
            // Missing specific mapping? Let's check model fields again vs image
        ];
        
        // Re-mapping based on actual model fields vs Image Labels
        // Image Labels Service: 
        // Ketersediaan Produk, Kesesuaian PO, Pengiriman Tepat Waktu, Info Kekosongan, 
        // Kelengkapan Dokumen, Kondisi Kendaraan, Sikap Sopan & Ramah, 
        // Kemudahan Berkomunikasi, Respon terhadap keluhan
        
        // Model Fields:
        // ketersediaan_produk, kesesuaian_po, info_kekosongan, ketepatan_waktu
        // info_pemberangkatan, kelengkapan_dokumen, kondisi_kendaraan, sikap_sales
        // kecakapan_sales, kemudahan_komunikasi, respon_keluhan

        // Let's map exactly to what's available and relevant
        $serviceCriteriaExact = [
            'ketersediaan_produk' => 'Ketersediaan Produk',
            'kesesuaian_po' => 'Kesesuaian PO',
            'ketepatan_waktu' => 'Pengiriman Tepat Waktu',
            'info_kekosongan' => 'Info Kekosongan',
            'kelengkapan_dokumen' => 'Kelengkapan Dokumen',
            'kondisi_kendaraan' => 'Kondisi Kendaraan',
            'sikap_sales' => 'Sikap Sopan & Ramah',
            'kemudahan_komunikasi' => 'Kemudahan Berkomunikasi',
            'respon_keluhan' => 'Respon terhadap keluhan'
        ];
        
        // 3. Calculate Averages
        $productStats = $this->calculateGroupStats($scores, $productCriteria);
        $serviceStats = $this->calculateGroupStats($scores, $serviceCriteriaExact);

        // 4. Summary Text Logic (Simple logic based on highest/lowest)
        // Pass month name if available
        $monthName = $month ? date('F', mktime(0, 0, 0, $month, 10)) : null;

        $summary = $this->generateSummary($productStats, $serviceStats, $year);

        // 5. Distributor Comparison Chart Data
        // Sort scores by value desc
        $distributorChartData = $scores->sortByDesc('score')
            ->take(15) // Limit to top 15 to fit on page
            ->map(function($score) {
                return [
                    'label' => $score->distributor->name ?? 'Unknown',
                    'value' => $score->score
                ];
            })->values();

        return view('reports.satisfaction', compact('productStats', 'serviceStats', 'summary', 'year', 'scores', 'monthName', 'distributorChartData'));
    }

    private function calculateGroupStats($scores, $criteria)
    {
        $stats = [];
        foreach ($criteria as $field => $label) {
            $total = 0;
            $count = 0;

            foreach ($scores as $score) {
                $val = 0;
                // Check if TP exists
                if (!is_null($score->{$field . '_tp'})) {
                    $val = $score->{$field . '_tp'};
                } 
                // Check if TN exists
                elseif (!is_null($score->{$field . '_tn'})) {
                    // Inverse score
                    $val = 6 - $score->{$field . '_tn'};
                }

                if ($val > 0) {
                    $total += $val;
                    $count++;
                }
            }

            $avg = $count > 0 ? round($total / $count, 1) : 0;
            $stats[$label] = $avg;
        }
        return $stats;
    }

    private function generateSummary($productStats, $serviceStats, $year)
    {
        // Find highest and lowest for product
        $prodMax = max($productStats);
        $prodMin = min($productStats);
        $prodMaxKeys = array_keys($productStats, $prodMax);
        $prodMinKeys = array_keys($productStats, $prodMin);

        // Find highest and lowest for service
        $servMax = max($serviceStats);
        $servMin = min($serviceStats);
        $servMaxKeys = array_keys($serviceStats, $servMax);
        $servMinKeys = array_keys($serviceStats, $servMin);

        // Calculate overall averages
        $prodAvg = round(array_sum($productStats) / count($productStats), 1);
        $servAvg = round(array_sum($serviceStats) / count($serviceStats), 1);

        return [
            'prod_max_val' => $prodMax,
            'prod_max_items' => implode(', ', $prodMaxKeys),
            'prod_min_val' => $prodMin,
            'prod_min_items' => implode(', ', $prodMinKeys),
            'serv_max_val' => $servMax,
            'serv_max_items' => implode(', ', $servMaxKeys),
            'serv_min_val' => $servMin,
            'serv_min_items' => implode(', ', $servMinKeys),
            'prod_avg' => $prodAvg,
            'serv_avg' => $servAvg
        ];
    }
}

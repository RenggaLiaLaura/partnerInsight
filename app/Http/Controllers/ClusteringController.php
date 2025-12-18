<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\ClusteringResult;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ClusteringController extends Controller
{
    public function index()
    {
        // Fetch current results to display
        $results = ClusteringResult::with('distributor')->get();
        
        // Prepare chart data
        // Prepare chart data (Line Chart - Sales Trend by Cluster)
        // Prepare chart data (Line Chart - Sales Trend by Cluster)
        $lineChartData = $results->sortBy('cluster_group')
            ->map(function($item) {
                return [
                    'label' => $item->distributor->name ?? 'Unknown',
                    'value' => $item->score_sales,
                    'cluster' => $item->cluster_group
                ];
            })->values();

        $clusterDistribution = $results->groupBy('cluster_group')->map->count();

        return view('clustering.index', compact('results', 'lineChartData', 'clusterDistribution'));
    }

    public function run(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action. Only Admin can run analysis.');
        }
        // Validate input
        $request->validate([
            'clusters' => 'required|integer|min:5|max:100',
            'max_iter' => 'required|integer|min:5|max:100',
        ]);

        // Save config to session
        $k = (int) $request->input('clusters', 3);
        $maxIter = (int) $request->input('max_iter', 100);
        session(['clustering_k' => $k, 'clustering_max_iter' => $maxIter]);

        // Fetch data
        $distributors = Distributor::with(['satisfactionScores', 'salesPerformances'])->get();
        
        $data = [];
        foreach ($distributors as $distributor) {
            // Use collection methods (no parenthesis) to avoid N+1 queries
            $satisfaction = $distributor->satisfactionScores->avg('score') ?? 0;
            $sales = $distributor->salesPerformances->sum('amount') ?? 0;
            
            $data[] = [
                'id' => $distributor->id,
                'features' => [$satisfaction, $sales] // Feature 0: Sat, Feature 1: Sales
            ];
        }

        if (empty($data)) {
            return redirect()->route('clustering.index')->with('error', 'No data available for clustering.');
        }

        // Run K-Means
        $kmeans = new \App\Services\KMeansClustering();
        $results = $kmeans->perform($data, $k, $maxIter);

        // Analyze clusters to assign meaningful labels
        // We calculate the average Sales and Satisfaction for each cluster index
        $clusterStats = [];
        foreach ($results as $res) {
            $clusterId = $res['cluster'];
            // Find original data
            $original = collect($data)->firstWhere('id', $res['id']);
            
            if (!isset($clusterStats[$clusterId])) {
                $clusterStats[$clusterId] = ['sat_sum' => 0, 'sales_sum' => 0, 'count' => 0];
            }
            $clusterStats[$clusterId]['sat_sum'] += $original['features'][0];
            $clusterStats[$clusterId]['sales_sum'] += $original['features'][1];
            $clusterStats[$clusterId]['count']++;
        }

        // Calculate averages
        foreach ($clusterStats as $id => &$stats) {
            $stats['avg_sat'] = $stats['sat_sum'] / $stats['count'];
            $stats['avg_sales'] = $stats['sales_sum'] / $stats['count'];
        }
        unset($stats);

        // Sort clusters by "value" (e.g., combined score or just sales) to assign labels
        // Let's sort by avg_sales descending
        uasort($clusterStats, function ($a, $b) {
            return $b['avg_sales'] <=> $a['avg_sales'];
        });

        // Assign labels based on rank
        // If K=3: Top = Loyal, Middle = Potensial, Bottom = Berisiko
        // If K!=3: Cluster 1, Cluster 2, etc. (Ordered by value)
        $labels = [];
        $rank = 0;
        $totalClusters = count($clusterStats);
        // 6. Smart Labeling
        // Calculate average metrics for each cluster to determine its label
        $clusterMetrics = [];
        foreach ($results as $res) {
            $c = $res['cluster'];
            if (!isset($clusterMetrics[$c])) {
                $clusterMetrics[$c] = ['sales' => 0, 'satisfaction' => 0, 'count' => 0];
            }
            // Use original values for metrics
            $original = collect($data)->firstWhere('id', $res['id']);
            $clusterMetrics[$c]['sales'] += $original['features'][1];
            $clusterMetrics[$c]['satisfaction'] += $original['features'][0];
            $clusterMetrics[$c]['count']++;
        }

        // Calculate averages
        foreach ($clusterMetrics as $c => $metrics) {
            $clusterMetrics[$c]['avg_sales'] = $metrics['sales'] / $metrics['count'];
            $clusterMetrics[$c]['avg_satisfaction'] = $metrics['satisfaction'] / $metrics['count'];
        }

        // Sort clusters by Sales (primary) and Satisfaction (secondary) descending
        uasort($clusterMetrics, function ($a, $b) {
            if ($a['avg_sales'] == $b['avg_sales']) {
                return $b['avg_satisfaction'] <=> $a['avg_satisfaction'];
            }
            return $b['avg_sales'] <=> $a['avg_sales'];
        });

        // Assign labels based on rank
        $labels = [];
        $rank = 0;
        $totalClusters = count($clusterMetrics);
        
        foreach ($clusterMetrics as $c => $metrics) {
            $label = '';
            if ($totalClusters == 3) {
                if ($rank == 0) $label = 'Loyal';
                elseif ($rank == 1) $label = 'Potensial';
                else $label = 'Berisiko';
            } else {
                // Generic fallback for other K values
                if ($rank == 0) $label = 'Loyal';
                elseif ($rank < $totalClusters / 2) $label = 'Potensial';
                else $label = 'Berisiko';
            }
            // Combine Cluster ID and Category
            $labels[$c] = "Cluster " . ($c + 1) . " - " . $label;
            $rank++;
        }

        // Save results
        DB::transaction(function () use ($results, $labels, $data) {
            foreach ($results as $res) {
                $distributorId = $res['id'];
                $clusterIndex = $res['cluster'];
                $label = $labels[$clusterIndex] ?? 'Unknown';
                
                // Find original values again for saving
                $original = collect($data)->firstWhere('id', $distributorId);
                
                ClusteringResult::updateOrCreate(
                    ['distributor_id' => $distributorId],
                    [
                        'cluster_group' => $label,
                        'score_satisfaction' => $original['features'][0],
                        'score_sales' => $original['features'][1]
                    ]
                );
            }
        });

        // Send Notification
        Notification::send(
            Auth::id(),
            'clustering_complete',
            'Clustering Analysis Completed',
            "The analysis for K={$k} has been successfully updated."
        );

        return redirect()->route('clustering.index')->with('success', 'Clustering analysis completed successfully.');
    }

    public function export()
    {
        // Allowed for all authenticated users (or at least Staff/Manager/Admin)
        return Excel::download(new \App\Exports\ClusteringExport, 'clustering_results_' . date('Y-m-d') . '.xlsx');
    }

    public function generateReport()
    {
        // Fetch all results with distributor details
        $results = ClusteringResult::with('distributor')->get();

        if ($results->isEmpty()) {
            return redirect()->back()->with('error', 'No clustering results found. Please run analysis first.');
        }

        // Group by cluster and calculate averages
        $clusters = $results->groupBy('cluster_group');
        $chartData = [];

        foreach ($clusters as $group => $items) {
            $avgSat = $items->avg('score_satisfaction');
            $avgSales = $items->avg('score_sales');
            
            $chartData[] = [
                'cluster' => $group,
                'avg_satisfaction' => round($avgSat, 2),
                'avg_sales' => round($avgSales, 0),
                'count' => $items->count()
            ];
        }

        return view('reports.clustering', compact('chartData', 'clusters', 'results'));
    }
}

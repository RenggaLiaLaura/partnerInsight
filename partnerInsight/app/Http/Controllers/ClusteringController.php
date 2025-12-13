<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\ClusteringResult;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ClusteringController extends Controller
{
    public function index()
    {
        // Fetch current results to display
        $results = ClusteringResult::with('distributor')->get();
        
        // Prepare chart data
        // Prepare chart data (Line Chart - Sales Trend by Cluster)
        $lineChartData = $results->sortBy('distributor_id')
            ->groupBy('cluster_group')
            ->map(function($clusterItems) {
                return $clusterItems->map(function($item) {
                    return [
                        'label' => $item->distributor->name ?? 'Unknown',
                        'value' => $item->score_sales
                    ];
                })->values();
            });

        $clusterDistribution = $results->groupBy('cluster_group')->map->count();

        return view('clustering.index', compact('results', 'lineChartData', 'clusterDistribution'));
    }

    public function run(Request $request)
    {
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
        
        foreach ($clusterStats as $id => $stats) {
            if ($totalClusters == 3) {
                if ($rank == 0) $label = 'Loyal';
                elseif ($rank == 1) $label = 'Potensial';
                else $label = 'Berisiko';
            } else {
                $label = 'Cluster ' . ($rank + 1);
            }
            $labels[$id] = $label;
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

        return redirect()->route('clustering.index')->with('success', 'Clustering analysis completed successfully.');
    }

    public function export()
    {
        return Excel::download(new \App\Exports\ClusteringExport, 'clustering_results_' . date('Y-m-d') . '.xlsx');
    }
}

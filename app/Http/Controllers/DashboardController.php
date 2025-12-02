<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\SatisfactionScore;
use App\Models\SalesPerformance;
use App\Models\ClusteringResult;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Widget Stats
        $totalDistributors = Distributor::count();
        $avgSatisfaction = SatisfactionScore::avg('score') ?? 0;
        $totalSales = SalesPerformance::sum('amount') ?? 0;
        
        // Most common cluster
        $mostCommonCluster = ClusteringResult::select('cluster_group', DB::raw('count(*) as total'))
            ->groupBy('cluster_group')
            ->orderByDesc('total')
            ->first();
        $topCluster = $mostCommonCluster ? $mostCommonCluster->cluster_group : '-';

        // Chart Data 1: Line Chart (Sales Trend by Cluster)
        // Group distributors by cluster and show their sales
        $lineChartData = ClusteringResult::with('distributor')
            ->orderBy('cluster_group', 'asc') // Sort by cluster to keep them together
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->distributor->name ?? 'Unknown',
                    'value' => $item->score_sales,
                    'cluster' => $item->cluster_group
                ];
            });

        // Chart Data 2: Pie Chart (Cluster Distribution)
        $clusterDistribution = ClusteringResult::select('cluster_group', DB::raw('count(*) as total'))
            ->groupBy('cluster_group')
            ->pluck('total', 'cluster_group');

        // Table Data: Top 5 Distributors by Sales Quantity
        $distributors = Distributor::with(['clusteringResult', 'satisfactionScores', 'salesPerformances'])
            ->withSum('salesPerformances', 'amount')
            ->orderByDesc('sales_performances_sum_amount')
            ->take(5)
            ->get();

        // Format Total Sales
        // Format Total Sales (Quantity)
        if ($totalSales >= 1000000) {
            $formattedSales = number_format($totalSales / 1000000, 1) . 'M';
        } elseif ($totalSales >= 1000) {
            $formattedSales = number_format($totalSales / 1000, 1) . 'K';
        } else {
            $formattedSales = number_format($totalSales, 0, ',', '.');
        }

        return view('dashboard', compact(
            'totalDistributors', 
            'avgSatisfaction', 
            'totalSales', 
            'formattedSales',
            'topCluster',
            'lineChartData',
            'clusterDistribution',
            'distributors'
        ));
    }
}

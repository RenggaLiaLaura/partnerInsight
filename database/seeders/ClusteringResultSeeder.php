<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClusteringResult;
use App\Models\Distributor;

class ClusteringResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributors = Distributor::with(['salesPerformances', 'satisfactionScores'])->get();
        
        foreach ($distributors as $distributor) {
            // Calculate actual scores (Quantity)
            $salesQty = $distributor->salesPerformances->sum('amount');
            $satisfaction = $distributor->satisfactionScores->avg('score') ?? 0;

            // Determine cluster based on Quantity
            $cluster = 'Cluster 3 - Berisiko'; // Default
            
            if ($salesQty > 80000 && $satisfaction > 4.0) {
                $cluster = 'Cluster 1 - Loyal';
            } elseif ($salesQty > 40000 || $satisfaction > 3.5) {
                $cluster = 'Cluster 2 - Potensial';
            }

            ClusteringResult::create([
                'distributor_id' => $distributor->id,
                'cluster_group' => $cluster,
                'score_satisfaction' => $satisfaction,
                'score_sales' => $salesQty,
            ]);
        }
    }
}

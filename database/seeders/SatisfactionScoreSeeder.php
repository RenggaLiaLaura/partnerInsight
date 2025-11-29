<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SatisfactionScore;
use App\Models\Distributor;
use Carbon\Carbon;

class SatisfactionScoreSeeder extends Seeder
{
    public function run()
    {
        $distributors = Distributor::all();

        foreach ($distributors as $distributor) {
            // Generate data for each month of 2024
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create(2024, $month, 1);
                
                // Generate random scores for 6 dimensions (1-5)
                $q1 = mt_rand(3, 5); // Quality Product
                $q2 = mt_rand(3, 5); // Spec Conformity
                $q3 = mt_rand(3, 5); // Quality Consistency
                $q4 = mt_rand(3, 5); // Price vs Quality
                $q5 = mt_rand(3, 5); // Product Condition
                $q6 = mt_rand(3, 5); // Packaging Condition

                // Calculate average score and round to nearest integer
                $score = round(($q1 + $q2 + $q3 + $q4 + $q5 + $q6) / 6);

                SatisfactionScore::create([
                    'distributor_id' => $distributor->id,
                    'quality_product' => $q1,
                    'spec_conformity' => $q2,
                    'quality_consistency' => $q3,
                    'price_quality' => $q4,
                    'product_condition' => $q5,
                    'packaging_condition' => $q6,
                    'score' => $score,
                    'period' => $date->format('Y-m-d'),
                ]);
            }
        }
    }
}

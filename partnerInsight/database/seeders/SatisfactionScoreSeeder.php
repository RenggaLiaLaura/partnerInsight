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
                
                // Random score between 3.0 and 5.0
                $score = mt_rand(30, 50) / 10;

                SatisfactionScore::create([
                    'distributor_id' => $distributor->id,
                    'score' => $score,
                    'period' => $date->format('Y-m-d'),
                ]);
            }
        }
    }
}

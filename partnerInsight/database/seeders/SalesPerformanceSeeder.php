<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesPerformance;
use App\Models\Distributor;
use Carbon\Carbon;

class SalesPerformanceSeeder extends Seeder
{
    public function run()
    {
        $distributors = Distributor::all();

        foreach ($distributors as $distributor) {
            // Generate data for each month of 2024
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create(2024, $month, 1);
                
                // Random sales between 50,000,000 and 500,000,000
                $amount = mt_rand(50000000, 500000000);

                SalesPerformance::create([
                    'distributor_id' => $distributor->id,
                    'amount' => $amount,
                    'period' => $date->format('Y-m-d'),
                ]);
            }
        }
    }
}

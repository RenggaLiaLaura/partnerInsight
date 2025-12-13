<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\DistributorSeeder;
use Database\Seeders\SatisfactionScoreSeeder;
use Database\Seeders\SalesPerformanceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            DistributorSeeder::class,
            SatisfactionScoreSeeder::class,
            SalesPerformanceSeeder::class,
        ]);
    }
}

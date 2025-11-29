<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Distributor;
use Faker\Factory as Faker;

class DistributorSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID'); // Use Indonesian locale

        $regions = [
            'ACEH', 'SUMATERA UTARA', 'SUMATERA BARAT', 'RIAU', 'JAMBI', 
            'SUMATERA SELATAN', 'BENGKULU', 'LAMPUNG', 'DKI JAKARTA', 'JAWA BARAT', 
            'JAWA TENGAH', 'DI YOGYAKARTA', 'JAWA TIMUR', 'BANTEN', 'BALI', 
            'NUSA TENGGARA BARAT', 'NUSA TENGGARA TIMUR', 'KALIMANTAN BARAT', 
            'KALIMANTAN TENGAH', 'KALIMANTAN SELATAN', 'KALIMANTAN TIMUR', 
            'SULAWESI UTARA', 'SULAWESI TENGAH', 'SULAWESI SELATAN', 'SULAWESI TENGGARA', 
            'GORONTALO', 'SULAWESI BARAT', 'MALUKU', 'MALUKU UTARA', 'PAPUA', 'PAPUA BARAT'
        ];

        for ($i = 0; $i < 50; $i++) {
            Distributor::create([
                'name' => 'PT. ' . $faker->company,
                'region' => $faker->randomElement($regions),
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
            ]);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Distributor;
use App\Models\SatisfactionScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SatisfactionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_satisfaction_score_with_metrics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $distributor = Distributor::create([
            'name' => 'Test Distributor',
            'phone' => '081234567890',
            'region' => 'Test Region',
            'address' => 'Test Address',
        ]);

        $data = [
            'distributor_id' => $distributor->id,
            'quality_product' => 4,
            'spec_conformity' => 5,
            'quality_consistency' => 4,
            'price_quality' => 5,
            'product_condition' => 4,
            'packaging_condition' => 5,
            'period' => '2023-10-01',
        ];

        $response = $this->actingAs($admin)->post(route('satisfaction.store'), $data);

        $response->assertRedirect(route('satisfaction.index'));
        
        // Expected score: (4+5+4+5+4+5) / 6 = 27 / 6 = 4.5
        $this->assertDatabaseHas('satisfaction_scores', array_merge($data, ['score' => 4.5]));
    }

    public function test_admin_can_update_satisfaction_score_with_metrics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $distributor = Distributor::create([
            'name' => 'Test Distributor',
            'phone' => '081234567890',
            'region' => 'Test Region',
            'address' => 'Test Address',
        ]);

        $score = SatisfactionScore::create([
            'distributor_id' => $distributor->id,
            'quality_product' => 3,
            'spec_conformity' => 3,
            'quality_consistency' => 3,
            'price_quality' => 3,
            'product_condition' => 3,
            'packaging_condition' => 3,
            'score' => 3.0,
            'period' => '2023-09-01',
        ]);

        $newData = [
            'distributor_id' => $distributor->id,
            'quality_product' => 5,
            'spec_conformity' => 5,
            'quality_consistency' => 5,
            'price_quality' => 5,
            'product_condition' => 5,
            'packaging_condition' => 5,
            'period' => '2023-10-01',
        ];

        $response = $this->actingAs($admin)->put(route('satisfaction.update', $score), $newData);

        $response->assertRedirect(route('satisfaction.index'));
        
        // Expected score: 5.0
        $this->assertDatabaseHas('satisfaction_scores', array_merge($newData, ['score' => 5.0]));
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Distributor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistributorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_create_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('distributors.create'));

        $response->assertStatus(200);
        $response->assertViewIs('distributors.create');
    }

    public function test_manager_cannot_view_create_page()
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($manager)->get(route('distributors.create'));

        $response->assertStatus(403); // Or 404 if route not matched, but should be 403 due to middleware
    }

    public function test_admin_can_store_distributor()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $data = [
            'name' => 'Test Distributor',
            'phone' => '081234567890',
            'region' => 'Test Region',
            'address' => 'Test Address',
        ];

        $response = $this->actingAs($admin)->post(route('distributors.store'), $data);

        $response->assertRedirect(route('distributors.index'));
        $this->assertDatabaseHas('distributors', $data);
    }

    public function test_admin_can_view_edit_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $distributor = Distributor::create([
            'name' => 'Old Name',
            'phone' => '081234567890',
            'region' => 'Old Region',
            'address' => 'Old Address',
        ]);

        $response = $this->actingAs($admin)->get(route('distributors.edit', $distributor));

        $response->assertStatus(200);
        $response->assertViewIs('distributors.edit');
    }

    public function test_admin_can_update_distributor()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $distributor = Distributor::create([
            'name' => 'Old Name',
            'phone' => '081234567890',
            'region' => 'Old Region',
            'address' => 'Old Address',
        ]);

        $newData = [
            'name' => 'New Name',
            'phone' => '089876543210',
            'region' => 'New Region',
            'address' => 'New Address',
        ];

        $response = $this->actingAs($admin)->put(route('distributors.update', $distributor), $newData);

        $response->assertRedirect(route('distributors.index'));
        $this->assertDatabaseHas('distributors', $newData);
    }

    public function test_admin_can_delete_distributor()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $distributor = Distributor::create([
            'name' => 'To Delete',
            'phone' => '081234567890',
            'region' => 'Region',
            'address' => 'Address',
        ]);

        $response = $this->actingAs($admin)->delete(route('distributors.destroy', $distributor));

        $response->assertRedirect(route('distributors.index'));
        $this->assertDatabaseMissing('distributors', ['id' => $distributor->id]);
    }
}

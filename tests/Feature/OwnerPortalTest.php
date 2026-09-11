<?php

namespace Tests\Feature;

use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Owner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_login_screen_can_be_rendered()
    {
        $response = $this->get('/owner/login');
        $response->assertStatus(200);
    }

    public function test_owner_can_authenticate_with_valid_credentials()
    {
        $owner = Owner::factory()->create([
            'email' => 'testowner@fleet.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/owner/login', [
            'email' => 'testowner@fleet.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($owner, 'owner');
        $response->assertRedirect(route('owner.dashboard'));
    }

    public function test_owner_dashboard_returns_strictly_isolated_stats()
    {
        $owner1 = Owner::factory()->create();
        $owner2 = Owner::factory()->create();

        // Create cars for Owner 1
        $car1 = Car::factory()->create(['owner_id' => $owner1->id, 'status' => 'approved', 'car_price_per_day' => 100]);
        $driver1 = Driver::create([
            'owner_id' => $owner1->id,
            'name' => 'Driver 1',
            'phone' => '1234567890',
            'license_number' => 'LIC-01',
            'experience_years' => '5',
            'status' => 'active',
        ]);

        // Create cars for Owner 2
        Car::factory()->create(['owner_id' => $owner2->id, 'status' => 'approved', 'car_price_per_day' => 200]);

        $customer = Customer::factory()->create();
        BookingCar::create([
            'car_id' => $car1->id,
            'customer_id' => $customer->id,
            'total_price' => 500,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($owner1, 'owner')->get('/owner/dashboard');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('owner/Dashboard')
            ->has('stats')
            ->where('stats.totalCars', 1)
            ->where('stats.totalDrivers', 1)
            ->where('stats.totalRevenue', 500)
        );
    }

    public function test_owner_can_create_vehicle_in_fleet()
    {
        $owner = Owner::factory()->create();

        $response = $this->actingAs($owner, 'owner')->post('/owner/cars', [
            'car_name' => 'Toyota',
            'car_model' => 'RAV4',
            'car_number' => 'BA 2 PA 9999',
            'number_of_seats' => 5,
            'car_price_per_day' => 85,
            'car_price_per_km' => 20,
            'available' => true,
        ]);

        $response->assertRedirect(route('owner.cars.index'));
        $this->assertDatabaseHas('cars', [
            'owner_id' => $owner->id,
            'car_number' => 'BA 2 PA 9999',
            'status' => 'pending',
        ]);
    }

    public function test_owner_cannot_modify_cars_belonging_to_another_owner()
    {
        $owner1 = Owner::factory()->create();
        $owner2 = Owner::factory()->create();

        $otherCar = Car::factory()->create(['owner_id' => $owner2->id, 'car_name' => 'Other Make']);

        $response = $this->actingAs($owner1, 'owner')->put("/owner/cars/{$otherCar->id}", [
            'car_name' => 'Hacked Car',
            'car_model' => 'Model',
            'car_number' => $otherCar->car_number,
            'number_of_seats' => 4,
            'car_price_per_day' => 10,
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('cars', [
            'id' => $otherCar->id,
            'car_name' => 'Hacked Car',
        ]);
    }

    public function test_owner_can_update_theme_style_preference()
    {
        $owner = Owner::factory()->create(['theme_style' => 'dark']);

        $response = $this->actingAs($owner, 'owner')->postJson('/owner/theme-style', [
            'theme_style' => 'midnight',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('owners', [
            'id' => $owner->id,
            'theme_style' => 'midnight',
        ]);
    }
}

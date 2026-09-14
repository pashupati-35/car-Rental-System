<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'owner_id' => Owner::factory(),
            'car_name' => fake()->randomElement(['Toyota', 'Hyundai', 'Ford', 'Honda']),
            'car_model' => fake()->word(),
            'car_number' => fake()->unique()->bothify('BA ? PA ####'),
            'number_of_seats' => 5,
            'car_price_per_day' => fake()->randomFloat(2, 40, 150),
            'car_price_per_km' => fake()->randomFloat(2, 10, 30),
            'available' => true,
            'status' => 'approved',
        ];
    }
}

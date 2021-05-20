<?php

namespace Database\Factories;

use App\Models\Target;
use Illuminate\Database\Eloquent\Factories\Factory;

class TargetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Target::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'year'          => $this->faker->numberBetween(2020, 2023),
            'month'         => $this->faker->numberBetween(1, 12),
            'value'         => $this->faker->randomNumber(5),
            'model_type'    => $this->faker->text(10),
            'model'         => $this->faker->text(10),
            'type'          => $this->faker->randomElement(['channel', 'sales', 'category']),
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}

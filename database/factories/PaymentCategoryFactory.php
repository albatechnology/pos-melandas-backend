<?php

namespace Database\Factories;

use App\Models\PaymentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = ['Transfer', 'Split Payment', 'Credit Card'];

        return [
            //
            'name'          => $this->faker->randomElement($name),
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}

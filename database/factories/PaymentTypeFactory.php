<?php

namespace Database\Factories;

use App\Models\PaymentCategory;
use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentType::class;

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
            'name'                  => $this->faker->name,
            'code'                  => $this->faker->numerify('code-###'),
            'slug'                  => Str::slug($this->faker->name),
            'require_approval'      => $this->faker->numberBetween(0,1),
            'payment_category_id'   => PaymentCategory::all()->random()->id,
            'created_at'            => now(),
            'updated_at'            => now(),
        ];
    }
}

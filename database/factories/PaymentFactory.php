<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = ['waiting', 'rejected', 'approved'];

        return [
            //
            'amount'            => $this->faker->randomNumber(5),
            'reference'         => $this->faker->text($maxNbChars = 50),
            'status'            => $this->faker->randomElement($status),
            'payment_type_id'   => PaymentType::all()->random()->id,
            'approved_by_id'    => User::all()->random()->id,
            'added_by_id'       => User::all()->random()->id,
            'created_at'        => now(),
            'updated_at'        => now()
        ];
    }
}

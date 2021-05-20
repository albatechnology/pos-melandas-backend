<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Promo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PromoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Promo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'          => $this->faker->name,
            'description'   => $this->faker->text(50),
            'start_date'    => Carbon::now()->subDays(30)->format('Y-m-d'),
            'end_date'      => Carbon::now()->addDays(30)->format('Y-m-d'),
            'channel_id'    => Channel::all()->random()->id,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type'              => LeadType::getRandomValue(),
            'status'            => LeadStatus::getRandomValue(),
            'is_new_customer'   => $this->faker->numberBetween(0,1),
            'label'             => $this->faker->domainWord,
            'customer_id'       => Customer::first()->id ?? Customer::factory()->withAddress()->create()->id,
            'channel_id'        => Channel::first()->id ?? Channel::factory()->create()->id,
            'user_id'           => User::whereIsSales()->first()->id ?? User::factory()->sales()->create()->id,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}

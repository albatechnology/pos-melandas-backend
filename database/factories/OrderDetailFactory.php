<?php

namespace Database\Factories;

use App\Enums\OrderDetailStatus;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $price                = $this->faker->randomNumber(5);
        $quantity             = $this->faker->numberBetween(1, 5);
        $original_total_price = $price * $quantity;
        $total_discount       = $this->faker->numberBetween(0, 5) * 10 / 100 * $original_total_price;
        $total_price          = $original_total_price - $total_discount;
        $product_unit         = ProductUnit::factory()->create();

        return [
            'status'             => OrderDetailStatus::getRandomValue(),
            'quantity'           => $quantity,
            'fulfilled_quantity' => $this->faker->numberBetween(0, 5),
            'stock_fulfilment'   => null,
            'unit_price'         => $price,
            'total_discount'     => $total_discount,
            'total_price'        => $total_price,
            'order_id'           => Order::factory()->create()->id,
            'company_id'         => Company::first()->id ?? Company::factory()->create()->id,
            'records'            => [
                'product_unit' => $product_unit->toRecord(),
                'product'      => $product_unit->product->toRecord()
            ],
            'created_at'         => now(),
            'updated_at'         => now(),
        ];
    }
}

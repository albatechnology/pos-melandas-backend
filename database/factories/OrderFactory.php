<?php

namespace Database\Factories;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\TaxInvoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $price = $this->faker->randomNumber(5);

        $address  = Address::factory()->create();

        return [
            'note'                => $this->faker->text($maxNbChars = 50),
            'invoice_number'      => sprintf('INV%s%04d', now()->format('Ymd'), $this->faker->randomDigit % 10000),
            'tax_invoice_sent'    => $this->faker->numberBetween(0, 1),
            'total_discount'      => 0,
            'total_price'         => $price,
            'status'              => OrderStatus::getRandomValue(),
            'payment_status'      => OrderPaymentStatus::getRandomValue(),
            'user_id'             => User::all()->whereNotNull('type')->random()->id,
            'customer_id'         => Customer::first()->id ?? Customer::factory()->create()->id,
            'channel_id'          => Channel::first()->id ?? Channel::factory()->create()->id,
            'company_id'          => Company::first()->id ?? Company::factory()->create()->id,
            'lead_id'             => Lead::first()->id ?? Lead::factory()->create()->id,
            'records'             => [
                'billing_address'  => $address->toRecord(),
                'shipping_address' => $address->toRecord(),
                'tax_invoice'      => TaxInvoice::factory()->create()->toRecord(),
                'discount'         => null //Discount::factory()->create()->toRecord(),
            ],
            'created_at'          => now(),
            'updated_at'          => now(),
        ];
    }

    public function withOrderDetails()
    {
        return $this->afterCreating(function (Order $order) {
            OrderDetail::factory()->count(2)->for($order)->create();
        });
    }
}

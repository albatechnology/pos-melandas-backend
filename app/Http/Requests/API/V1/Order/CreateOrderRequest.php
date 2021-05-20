<?php

namespace App\Http\Requests\API\V1\Order;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Models\Address;
use App\Models\Lead;
use App\Models\Order;
use App\Models\ProductUnit;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends BaseApiRequest
{
    protected ?string $model = Order::class;

    public static function getSchemas(): array
    {
        return [
            Schema::array('items')->items(
                Schema::object()->properties(
                    Schema::integer('id')->example(1)->description('The product unit id to add to cart'),
                    Schema::integer('quantity')->example(1)
                ),
            ),
            Schema::integer('discount_id')->example(1),
            Schema::integer('expected_price')
                  ->example(1000)
                  ->description('Provide expected price of the order for consistency checking.')
                  ->nullable(),
            Schema::integer('shipping_address_id')->example(1),
            Schema::integer('billing_address_id')->example(1),
            Schema::integer('tax_invoice_id')->example(1),
            Schema::integer('lead_id')->example(1),
            Schema::string('note')->example('Note placed on order'),
            Schema::integer('shipping_fee')->example(10000),
            Schema::integer('packing_fee')->example(10000),
        ];
    }

    protected static function data()
    {
        return [];
    }

    public function rules(): array
    {
        $lead     = $this->input('lead_id') ? Lead::with('customer')->where('id', $this->input('lead_id'))->first() : null;
        $customer = $lead ? $lead->customer : null;

        return [
            'items'            => 'required|array',
            'items.*.id'       => [
                'required',
                function ($attribute, $value, $fail) {
                    $unit = ProductUnit::tenanted()->whereActive()->where('id', $value)->first();

                    if (!$unit) {
                        $fail('Invalid or inactive product unit.');
                    }
                }
            ],
            'items.*.quantity' => 'required|integer|min:1',

            'discount_id' => [
                'nullable', 'integer', 'exists:discounts,id',
            ],

            'expected_price'      => 'nullable|integer',
            'shipping_address_id' => [
                'required',
                function ($attribute, $value, $fail) use ($customer) {
                    if (!$customer) return null;

                    $address = Address::where('customer_id', $customer->id)
                                      ->where('id', $value)
                                      ->get();

                    if ($address->isEmpty()) $fail('Invalid shipping address selected.');
                },
            ],

            'billing_address_id' => [
                'required',
                function ($attribute, $value, $fail) use ($customer) {
                    if (!$customer) return null;

                    $address = Address::where('customer_id', $customer->id)
                                      ->where('id', $value)
                                      ->get();

                    if ($address->isEmpty()) $fail('Invalid billing address selected.');
                },
            ],

            'tax_invoice_id' => [
                'nullable',
                Rule::unique('tax_invoices', 'id')->where('customer_id', $customer->id)
            ],

            'lead_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    // currently only allow the sales that owns the lead to select the lead
                    if (!user()->leads()->where('id', $value)->first()) $fail('The selected lead does not belong to this user.');
                },
            ],

            'note' => 'nullable|string',

            'packing_fee'  => 'nullable|integer|min:0',
            'shipping_fee' => 'nullable|integer|min:0',
        ];
    }
}
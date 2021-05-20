<?php

namespace Database\Factories;

use App\Models\Colour;
use App\Models\Company;
use App\Models\Covering;
use App\Models\Product;
use App\Models\ProductUnit;

class ProductUnitFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product  = Product::first() ?? Product::factory()->create();
        $colours  = Colour::where('product_brand_id', $product->product_brand_id)->get();
        $covering = Covering::all();

        return [
            'name'        => $this->faker->productName,
            'price'       => $this->faker->randomNumber(3) * 100000,
            'is_active'   => 1,
            'product_id'  => $product->id,
            'company_id'  => Company::first()->id ?? Company::factory()->create()->id,
            'colour_id'   => $colours->isNotEmpty() ? $colours->random()->id : Colour::factory()->create()->id,
            'covering_id' => $covering->isNotEmpty() ? $covering->random()->id : Covering::factory()->create()->id,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }

    public function forProduct(Product $product)
    {
        return $this->state(function (array $attributes) use ($product) {
            $colours = Colour::where('product_brand_id', $product->product_brand_id)->get();

            return [
                'name'       => $product->name . ' - ' . $this->faker->colorName,
                'product_id' => $product->id,
                'colour_id'  => $colours->isNotEmpty() ? $colours->random()->id : Colour::factory()->create()->id,
            ];
        });
    }

}

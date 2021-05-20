<?php

namespace Database\Factories;

use App\Models\Colour;
use App\Models\Company;
use App\Models\ProductBrand;

class ColourFactory extends BaseFactory
{
    protected $model = Colour::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'             => $this->faker->colorName,
            'description'      => $this->faker->sentence,
            'company_id'       => Company::first()->id ?? Company::factory()->create()->id,
            'product_brand_id' => ProductBrand::first()->id ?? ProductBrand::factory()->create()->id,
        ];
    }

    public function forBrand(ProductBrand $brand)
    {
        return $this->state(function (array $attributes) use ($brand) {
            return [
                'product_brand_id' => $brand->id,
            ];
        });
    }
}

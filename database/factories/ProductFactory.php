<?php

namespace Database\Factories;

use App\Models\Colour;
use App\Models\Company;
use App\Models\Covering;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductUnit;
use App\Models\ProductVersion;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Collection;

class ProductFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->productName;
        return [
            'name'                     => $this->faker->productName,
            'is_active'                => $this->faker->numberBetween(0, 1),
            'price'                    => $this->faker->randomNumber(3) * 100000,
            'company_id'               => Company::first()->id ?? Company::factory()->create()->id,
            'product_brand_id'         => ProductBrand::first()->id ?? ProductBrand::factory()->create()->id,
            'product_model_id'         => ProductModel::first()->id ?? ProductModel::factory()->create()->id,
            'product_version_id'       => ProductVersion::first()->id ?? ProductVersion::factory()->create()->id,
            'product_category_code_id' => ProductCategoryCode::factory()->create()->id,
            'created_at'               => now(),
            'updated_at'               => now(),
        ];
    }

    public function withProductUnits()
    {
        return $this->afterCreating(function (Product $product) {
            // create product units with all colour and covering combination

            $colours = $product->brand->colours;
            if ($colours->isEmpty()) {
                $colours = Colour::factory()->forBrand($product->brand)->count(2)->create();
            }

            $colours->each(function (Colour $colour) use ($product) {

                $coverings = Covering::all()->map(fn($q) => ['covering_id' => $q->id])->all();

                ProductUnit::factory()
                    ->count(count($coverings))
                    ->state(new Sequence(...$coverings))
                    ->create(
                        [
                            'name'       => $product->name,
                            'colour_id'  => $colour->id,
                            'product_id' => $product->id,
                        ]
                    );
            });
        });
    }

    public function withTags(Collection $tags)
    {
        return $this->afterCreating(function (Product $product) use ($tags) {
            $product->tags()->sync($tags->pluck('id'));
        });
    }
}

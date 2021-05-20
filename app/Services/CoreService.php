<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\Channel;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\User;
use BenSampo\Enum\Enum;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;


class CoreService
{
    const PRODUCT_NAMES = 'sample_product_name.csv';
    const MODEL_NAMES   = 'sample_model_name.csv';

    /**
     * Load csv sample data as array.
     * Used for seeder
     */
    public function loadSampleData(string $file): array
    {
        $path = storage_path('data/' . $file);
        $csv  = Reader::createFromPath($path, 'r');
        return collect($csv->getRecords())->collapse()->all();
    }

    /**
     * Get enum contracts to generate
     * @return array
     */
    public static function getEnumContracts(): array
    {
        return collect(Storage::disk('enums')->allFiles())
            // remove php extension
            ->map(fn(string $filename) => (string)Str::of($filename)->before('.'))

            // remove undesired class
            ->filter(function (string $classname) {
                return is_a(sprintf("App\\Enums\\%s", $classname), Enum::class, true) &&
                    ($classname != "BaseEnum");
            })

            // map to desired output format
            ->map(function (string $classname) {
                $class = sprintf("App\\Enums\\%s", $classname);
                return $class::getContract();
            })
            ->values()
            ->toArray();
    }

    public static function normalise(string $string): string
    {
        return (string)Str::of($string)
            ->trim()
            ->lower()
            ->replaceMatches('/[^a-z0-9]++/', '-');
    }

    /**
     * Create an instance of stock for all product unit.
     * @param Channel $channel
     */
    public static function createStocksForChannel(Channel $channel)
    {

        ProductUnit::query()
            ->select(['id', 'company_id'])
            ->where('company_id', 1)
            ->chunk(500, function ($units) use ($channel) {
                $ids = $units->pluck('id');

                // check existing stock to prevent duplicate
                $existingStockIds = Stock::query()
                    ->whereIn('id', $ids)
                    ->pluck('id');

                $stockData = $ids->diff($existingStockIds)
                    ->map(function (int $id) use ($channel) {
                        return [
                            'channel_id'      => $channel->id,
                            'product_unit_id' => $id
                        ];
                    });

                Stock::insert($stockData->all());
            });
    }

    /**
     * Create an instance of stock of a product unit for all channel
     * in the company.
     * @param ProductUnit $unit
     */
    public static function createStocksForProductUnit(ProductUnit $unit)
    {
        $ids = Channel::query()
            ->where('company_id', $unit->company_id)
            ->pluck('id');

        $existingId = Stock::query()
            ->where('product_unit_id', $unit->id)
            ->pluck('id');

        $stockData = $ids->diff($existingId)
            ->map(function (int $id) use ($unit) {
                return [
                    'channel_id'      => $id,
                    'product_unit_id' => $unit->id
                ];
            });

        Stock::insert($stockData->all());
    }

    /**
     * Helper function to grab API token for API documentation page.
     */
    public function getToken(): string
    {
        if (App::environment('production')) abort(403);

        $authType = request()->query('auth');

        if ($authType) {
            try {
                $type = UserType::fromValue($authType);
            } catch (Exception) {
                // sales as default
                $type = UserType::SALES();
            }
        } else {
            $type = UserType::SALES();
        }

        $user   = User::where('type', $type->value)->firstOrFail();
        $tokens = $user->tokens;

        return $tokens->isEmpty() ? $user->createToken('default')->plainTextToken : $tokens->first()->plain_text_token;

    }

    /**
     * Temporary method to assign random image to a model.
     * Image are selected in no particular order, but always uses
     * the same image for the same model
     *
     * @param Model $model
     * @return Application|UrlGenerator|string
     */
    public function getDummyImageUrl(Model $model)
    {
        $images = [
            "Banner.jpg",
            "Collecction_1.jpg",
            "Collecction_2.jpg",
            "Collecction_3.jpg",
            "Collecction_4.jpg",
            "Collecction_5.jpg",
            "collection_6.jpg",
            "New_1.jpg",
            "New_2.jpg",
            "New_3.jpg",
        ];

        $image = $images[$model->id % count($images)];
        return url('images/' . $image);
    }
}

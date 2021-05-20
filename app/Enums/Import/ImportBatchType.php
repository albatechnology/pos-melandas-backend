<?php

namespace App\Enums\Import;

use App\Enums\BaseEnum;
use App\Imports\ColourImport;
use App\Imports\CoveringImport;
use App\Imports\ProductBrandImport;
use App\Imports\ProductCategoryCodeImport;
use App\Imports\ProductImport;
use App\Imports\ProductModelImport;
use App\Imports\ProductUnitImport;
use App\Imports\ProductVersionImport;
use App\Models\Colour;
use App\Models\Covering;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductUnit;
use App\Models\ProductVersion;

/**
 * @method static static PRODUCT_BRAND()
 * @method static static PRODUCT_MODEL()
 * @method static static PRODUCT_VERSION()
 * @method static static PRODUCT_CATEGORY_CODE()
 * @method static static PRODUCT()
 * @method static static PRODUCT_UNIT()
 * @method static static COVERING()
 * @method static static COLOUR()
 */
final class ImportBatchType extends BaseEnum
{
    const PRODUCT_BRAND         = 0;
    const PRODUCT_MODEL         = 1;
    const PRODUCT_VERSION       = 2;
    const PRODUCT_CATEGORY_CODE = 3;
    const PRODUCT               = 4;
    const PRODUCT_UNIT          = 5;
    const COVERING              = 6;
    const COLOUR                = 7;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }

    public function getImporter(): ?string
    {
        return match ($this->value) {
            self::PRODUCT_BRAND => ProductBrandImport::class,
            self::PRODUCT_MODEL => ProductModelImport::class,
            self::PRODUCT_VERSION => ProductVersionImport::class,
            self::PRODUCT_CATEGORY_CODE => ProductCategoryCodeImport::class,
            self::PRODUCT => ProductImport::class,
            self::PRODUCT_UNIT => ProductUnitImport::class,
            self::COVERING => CoveringImport::class,
            self::COLOUR => ColourImport::class,
            default => null,
        };
    }

    public function getModel(): string
    {
        return match ($this->value) {
            self::PRODUCT_BRAND => ProductBrand::class,
            self::PRODUCT_MODEL => ProductModel::class,
            self::PRODUCT_VERSION => ProductVersion::class,
            self::PRODUCT_CATEGORY_CODE => ProductCategoryCode::class,
            self::PRODUCT => Product::class,
            self::PRODUCT_UNIT => ProductUnit::class,
            self::COVERING => Covering::class,
            self::COLOUR => Colour::class,
            default => null,
        };
    }
}
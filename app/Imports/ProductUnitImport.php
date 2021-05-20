<?php

namespace App\Imports;

use App\Models\ImportLine;
use App\Models\ProductUnit;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductUnitImport extends BaseImport implements ToModel, WithHeadingRow
{

    public static function getUniqueKey(): string
    {
        return 'sku';
    }

    /**
     * May provide export sample data
     * @return Collection
     */
    public static function getExportCollection(): Collection
    {
        return Collect([
            self::getHeader(),
            [
                'Test product unit',
                'Unit description',
                10000000,
                1,
                1, 1, 1, //relationships
                '123.32.132.32.13',
            ]
        ]);
    }

    public static function getHeader(): array
    {
        return [
            'name', 'description', 'price', 'is_active',
            'product_id', 'colour_id', 'covering_id',
            'sku'
        ];
    }

    public static function getModelName(): string
    {
        return ProductUnit::class;
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name'        => $line->data['name'],
            'description' => $line->data['description'] ?? null,
            'price'       => $line->data['price'],
            'is_active'   => $line->data['is_active'] ?? 0,
            'product_id'  => $line->data['product_id'],
            'colour_id'   => $line->data['colour_id'],
            'covering_id' => $line->data['covering_id'],
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name'        => $line->data['name'],
            'company_id'  => $this->batch->company_id,
            'description' => $line->data['description'] ?? null,
            'price'       => $line->data['price'],
            'is_active'   => $line->data['is_active'] ?? 0,
            'product_id'  => $line->data['product_id'],
            'colour_id'   => $line->data['colour_id'],
            'covering_id' => $line->data['covering_id'],
            'sku'         => $line->data['sku'],
        ];
    }

    protected function getValidationRule(): array
    {
        return [
            'name'        => 'required|min:1',
            'description' => 'nullable|min:1',
            'price'       => 'required|integer|min:1',
            'is_active'   => 'nullable|boolean',
            'product_id'  => [
                'required',
                Rule::exists('products', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
            'colour_id'   => [
                'required',
                Rule::exists('colours', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
            'covering_id' => [
                'required',
                Rule::exists('coverings', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
            'sku'         => 'required|min:1',
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\Covering;
use App\Models\ImportLine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoveringImport extends BaseImport implements ToModel, WithHeadingRow
{

    public static function getHeader(): array
    {
        return [
            'name', 'type'
        ];
    }

    public static function getModelName(): string
    {
        return Covering::class;
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name' => $line->data['name'],
            'type' => $line->data['type'] ?? null,
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name'       => $line->data['name'],
            'type'       => $line->data['type'] ?? null,
            'company_id' => $this->batch->company_id,
        ];
    }

    protected function getValidationRule(): array
    {
        return [
            'name' => 'required|min:1',
            'type' => 'nullable|min:1'
        ];
    }
}

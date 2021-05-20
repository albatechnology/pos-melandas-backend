<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Str;

abstract class BaseEnum extends Enum
{
    public static string $enum_description;
    public string $label;

    public function __construct($enumValue)
    {
        parent::__construct($enumValue);
        $this->label = Str::of($this->description)->replace('_', '')->lower()->ucfirst();
    }

    public static function getDefaultInstance()
    {
        $array = static::getInstances();
        return $array[array_key_first($array)];
    }

    public static function getDefaultValue(): string
    {
        return static::getDefaultInstance()->value;
    }

    public static function getContract()
    {
        return [
            'code' => (string) Str::of(get_called_class())->afterLast('\\'),
            'description' => static::$enum_description ?? 'Missing enum description',
            'enums' => collect(static::getInstances())->map(function (self $enum) {
                return ['value' => $enum->key, 'label' => ucfirst(strtolower($enum->description))];
            })->values()->toArray(),
        ];
    }
}

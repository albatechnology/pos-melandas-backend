<?php


namespace App\Traits;

trait CustomCastEnums
{
    public static function getEnumCasts()
    {
        return (new static())->enum_casts;
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        return array_merge(parent::getCasts(), $this->enum_casts ?? []);
    }


    /**
     * For api array casts enum to its key
     * @return array
     */
    public function toApiArray()
    {
        $enum_fields = collect($this->getEnumCasts())->map(fn($v, $key) => $this->$key->key)->all();
        return array_merge($this->toArray(), $enum_fields);
    }
}

<?php

namespace App\Models;

use App\Traits\CustomCastEnums;
use BenSampo\Enum\Traits\CastsEnums;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasFactory, CastsEnums, CustomCastEnums;

    protected $enum_casts = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

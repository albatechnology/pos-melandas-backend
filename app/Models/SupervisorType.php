<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperSupervisorType
 */
class SupervisorType extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'supervisor_types';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'level',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function supervisorTypeUsers()
    {
        return $this->hasMany(User::class, 'supervisor_type_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Area organizacional. Vive en la BD externa `timeit` (solo lectura desde esta app).
 * Cadena: empleados.Cargos_id -> cargos.Areas_id -> areas.id
 */
class Area extends Model
{
    protected $table = 'areas';
    protected $connection = 'timeit';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $appends = ['name'];

    protected $hidden = ['nombre', 'created_at', 'updated_at'];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'Areas_id', 'id');
    }

    public function getNameAttribute()
    {
        return $this->attributes['nombre'];
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nombre'] = $value;
    }
}

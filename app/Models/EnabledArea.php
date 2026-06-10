<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Area habilitada para usar la plataforma. Conexion propia `mysql`.
 * area_id referencia timeit.areas.id.
 */
class EnabledArea extends Model
{
    protected $connection = 'mysql';
    protected $table = 'enabled_areas';

    protected $fillable = ['area_id'];

    protected $casts = [
        'area_id' => 'integer',
    ];
}

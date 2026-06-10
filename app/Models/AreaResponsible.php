<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Responsable de un area. Conexion propia `mysql`.
 * area_id -> timeit.areas.id ; user_id -> timeit.usuarios.id
 */
class AreaResponsible extends Model
{
    protected $connection = 'mysql';
    protected $table = 'area_responsibles';

    protected $fillable = ['area_id', 'user_id'];

    protected $casts = [
        'area_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

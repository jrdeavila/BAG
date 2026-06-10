<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Funcionario especial: acceso a la plataforma aunque su area no este habilitada.
 * Conexion propia `mysql`. user_id -> timeit.usuarios.id
 */
class SpecialUser extends Model
{
    protected $connection = 'mysql';
    protected $table = 'special_users';

    protected $fillable = ['user_id', 'reason'];

    protected $casts = [
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

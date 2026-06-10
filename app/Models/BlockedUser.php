<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bloqueo individual: niega acceso aunque el area este habilitada (no aplica al superadmin).
 * Conexion propia `mysql`. user_id -> timeit.usuarios.id
 */
class BlockedUser extends Model
{
    protected $connection = 'mysql';
    protected $table = 'blocked_users';

    protected $fillable = ['user_id', 'reason'];

    protected $casts = [
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{

    protected $connection = 'mysql';
    protected $table = 'activities';

    protected $fillable = [
        'description',
        'user_id',
        'date',
        'status',
        'priority',
        'created_by',
        'start_time',
        'end_time',
        'observations',
    ];

    public $casts = [
        'date' => 'date',
        'observations' => 'string',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}

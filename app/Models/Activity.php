<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    protected $connection = 'mysql';
    protected $table = 'activities';

    protected $fillable = [
        'description',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'observations',
    ];

    public $casts = [
        'date' => 'date',
        'observations' => 'string',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

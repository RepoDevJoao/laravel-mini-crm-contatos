<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'score',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'score'        => 'integer',
        'processed_at' => 'datetime',
    ];
}
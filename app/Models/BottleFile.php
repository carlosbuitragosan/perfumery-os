<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BottleFile extends Model
{
    protected $fillable = [
        'user_id',
        'bottle_id',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
        'note',
    ];

    public function bottle()
    {
        return $this->belongsTo(Bottle::class);
    }
}

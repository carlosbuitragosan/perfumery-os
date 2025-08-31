<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'botanical', 'pyramid', 'families', 'functions', 'safety', 'effects', 'ifra_max_pct', 'notes'];

    protected $casts = [
        'pyramid' => 'array',
        'families' => 'array',
        'functions' => 'array',
        'safety' => 'array',
        'effects' => 'array',
        'ifra_max_pct' => 'float',
    ];
}

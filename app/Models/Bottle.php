<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bottle extends Model
{
    protected $fillable = [
        'supplier_name',
        'supplier_url',
        'batch_code',
        'method',
        'plant_part',
        'origin_country',
        'distillation_date',
        'purchase_date',
        'density',
        'volume_ml',
        'price',
        'notes',
    ];

    public function material()
    {
        $this->belongsTo(Material::class);
    }
}

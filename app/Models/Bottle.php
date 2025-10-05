<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bottle extends Model
{
    use HasFactory;

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

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}

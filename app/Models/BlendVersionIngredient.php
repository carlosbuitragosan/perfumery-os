<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlendVersionIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'blend_version_id',
        'material_id',
        'drops',
        'dilution',
    ];

    public function blendVersion()
    {
        return $this->belongsTo(BlendVersion::class, 'blend_version_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}

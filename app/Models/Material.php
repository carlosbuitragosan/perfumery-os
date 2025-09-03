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

    public function scopeSearch($query, ?string $searchTerm)
    {
        $searchTerm = trim((string) $searchTerm);

        // Return all materials for an empty query
        if ($searchTerm === '') {
            return $query;
        }

        $term = mb_strtolower($searchTerm);

        // Search name, botanical name and notes
        $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(COALESCE(botanical, "")) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(COALESCE(notes, "")) LIKE ?', ["%{$term}%"]);
        })

        // search pyramid, functions, families, effects, etc (arrays)
            ->orWhere(function ($j) use ($term) {
                $like = '%"'.$term.'%"';
                $j->whereRaw("LOWER(COALESCE(pyramid, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(families, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(functions, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(effects, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(safety, '')) LIKE ?", [$like]);
            });

    }
}

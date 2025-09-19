<?php

namespace App\Http\Controllers;

use App\Enums\ExtractionMethod;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum as EnumRule;

class BottleController extends Controller
{
    public function create(Material $material)
    {
        return view('bottles.create', compact('material'));
    }

    public function store(Material $material, Request $request)
    {
        $data = $request->validate([
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'supplier_url' => ['nullable', 'url'],
            'batch_code' => ['nullable', 'string', 'max:255'],
            'method' => ['required', new EnumRule(ExtractionMethod::class)],
            'plant_part' => ['nullable', 'string', 'max:255'],
            'origin_country' => ['nullable', 'string', 'max:255'],
            'distillation_date' => ['nullable', 'date'],
            'purchase_date' => ['nullable', 'date'],
            'density' => ['nullable', 'numeric', 'between:0,2'],
            'volume_ml' => ['nullable', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $material->bottles()->create($data);

        return redirect()->route('materials.show', $material);
    }
}

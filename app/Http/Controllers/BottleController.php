<?php

namespace App\Http\Controllers;

use App\Enums\ExtractionMethod;
use App\Models\Bottle;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum as EnumRule;

class BottleController extends Controller
{
    // show the form to create a bottle
    public function create(Material $material)
    {
        abort_if($material->user_id !== auth()->id(), 404);

        return view('bottles.create', compact('material'));
    }

    public function store(Material $material, Request $request)
    {
        abort_if($material->user_id !== auth()->id(), 404);

        $data = $request->validate([
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'supplier_url' => ['nullable', 'url'],
            'batch_code' => ['nullable', 'string', 'max:255'],
            'method' => ['required', new EnumRule(ExtractionMethod::class)],
            'plant_part' => ['nullable', 'string', 'max:255'],
            'origin_country' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'density' => ['nullable', 'numeric', 'between:0,2'],
            'volume_ml' => ['nullable', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:5120'],
        ]);

        $files = $request->file('files', []);
        unset($data['files']);

        $data['user_id'] = auth()->id();
        $bottle = $material->bottles()->create($data);

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();

            $storedPath = $file->storeAs("bottles/{$bottle->id}", $originalName, 'public');

            $bottle->files()->create([
                'user_id' => auth()->id(),
                'path' => $storedPath,
                'original_name' => $originalName,
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
                'note' => null,
            ]);
        }

        return redirect()->route('materials.show', $material);
    }

    public function edit(Bottle $bottle)
    {
        abort_if($bottle->user_id !== auth()->id(), 404);

        return view('bottles.edit', compact('bottle'));
    }

    public function update(Request $request, Bottle $bottle)
    {
        abort_if($bottle->user_id !== auth()->id(), 404);

        $data = $request->validate([
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'supplier_url' => ['nullable', 'url'],
            'batch_code' => ['nullable', 'string', 'max:255'],
            'method' => ['required', new EnumRule(ExtractionMethod::class)],
            'plant_part' => ['nullable', 'string', 'max:255'],
            'origin_country' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'density' => ['nullable', 'numeric', 'between:0,2'],
            'volume_ml' => ['nullable', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $bottle->update($data);
        $material = $bottle->material;

        return redirect(route('materials.show', $material).'#bottle-'.$bottle->id)
            ->with('ok', 'Bottle updated');
    }

    public function finish(Bottle $bottle)
    {
        abort_if($bottle->user_id !== auth()->id(), 404);

        $bottle->is_active = false;
        $bottle->save();

        return redirect()->route('materials.show', $bottle->material_id);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    // minimal index so redirect('/materials') works
    public function index()
    {
        $materials = Material::orderBy('name')->paginate(20);

        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('materials', 'name'),
            ],
            'category' => 'nullable|string|max:100',
            'botanical' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], ['name.unique' => 'A material with this name already exists.']);

        $data['name'] = trim($data['name']);
        if (! empty($data['botanical'])) {
            $data['botanical'] = trim($data['botanical']);
        }

        Material::create($data);

        return redirect()->route('materials.index')->with('ok', 'Material added');
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('materials', 'name')->ignore($material->id)],
            'category' => ['nullable', 'string', 'max:100'],
            'botanical' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['name'] = trim($data['name']);
        if (! empty($data['botanical'])) {
            $data['botanical'] = trim($data['botanical']);
        }
        $material->update($data);

        return redirect()->route('materials.index')->with('ok', 'Material updated');
    }
}

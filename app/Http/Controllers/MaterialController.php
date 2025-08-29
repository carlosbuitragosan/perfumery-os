<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

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
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'botanical' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Material::create($data);

        return redirect()->route('materials.index')->with('ok', 'Material added');
    }
}

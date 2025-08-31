<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    // Allowed vocabularies
    private array $familiesAllowed;

    private array $functionsAllowed;

    private array $safetyAllowed;

    private array $effectsAllowed;

    public function __construct()
    {
        $this->familiesAllowed = config('materials.families', []);
        $this->functionsAllowed = config('materials.functions', []);
        $this->safetyAllowed = config('materials.safety', []);
        $this->effectsAllowed = config('materials.effects', []);
    }

    // minimal index so redirect('/materials') works
    public function index()
    {
        $materials = Material::orderBy('name')->paginate(20);

        return view('materials.index', compact('materials'));
    }

    // Create form
    public function create()
    {
        return view('materials.create');
    }

    // Shared validation
    private function validateMaterials(Request $request, ?Material $material = null): array
    {
        $unique = Rule::unique('materials', 'name');
        if ($material) {
            $unique = $unique->ignore($material->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255', $unique],
            'botanical' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],

            'pyramid' => ['nullable', 'array'],
            'pyramid.*' => ['in:top,heart,base'],

            'families' => ['nullable', 'array'],
            'families.*' => [Rule::in($this->familiesAllowed)],

            'functions' => ['nullable', 'array'],
            'functions.*' => [Rule::in($this->functionsAllowed)],

            'safety' => ['nullable', 'array'],
            'safety.*' => [Rule::in($this->safetyAllowed)],

            'effects' => ['nullable', 'array'],
            'effects.*' => [Rule::in($this->effectsAllowed)],

            'ifra_max_pct' => ['nullable', 'numeric', 'between:0,100'],
        ]);
    }

    // Store
    public function store(Request $request)
    {
        $data = $this->validateMaterials($request);

        $data['name'] = trim($data['name']);
        if (! empty($data['botanical'])) {
            $data['botanical'] = trim($data['botanical']);
        }

        Material::create($data);

        return redirect()->route('materials.index')->with('ok', 'Material added');
    }

    // Edit form
    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    // Update
    public function update(Request $request, Material $material)
    {
        $data = $this->validateMaterials($request, $material);

        $data['name'] = trim($data['name']);
        if (! empty($data['botanical'])) {
            $data['botanical'] = trim($data['botanical']);
        }
        $material->update($data);

        return redirect()->route('materials.index')->with('ok', 'Material updated');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Blend;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlendController extends Controller
{
    public function create()
    {
        $materials = Material::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('blends.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'materials' => ['required', 'array', 'min:1'],
            'materials.*.material_id' => ['required', 'integer', 'exists:materials,id'],
            'materials.*.drops' => ['required', 'integer', 'min:1', 'max:999'],
            'materials.*.dilution' => ['required', 'integer', 'in:25,10,1'],
        ]);

        $validator->after(function ($validator) {
            $materials = $validator->getData()['materials'] ?? [];

            $ids = collect($materials)
                ->pluck('material_id')
                ->filter();

            if ($ids->count() !== $ids->unique()->count()) {
                $validator->errors()->add('materials', 'You can\'t use the same material twice.');
            }
        });

        $data = $validator->validate();

        $blend = Blend::create([
            'user_id' => auth()->id(),
            'name' => trim($data['name']),
        ]);

        $version = $blend->versions()->create([
            'version' => '1.0',
        ]);

        foreach ($data['materials'] as $row) {
            $version->ingredients()->create([
                'material_id' => $row['material_id'],
                'drops' => $row['drops'],
                'dilution' => $row['dilution'],
            ]);
        }

        return redirect()->route('blends.show', $blend);
    }

    public function show(Blend $blend)
    {
        abort_unless($blend->user_id === auth()->id(), 404);

        $version = $blend->versions()
            ->where('version', '1.0')
            ->with(['ingredients.material'])
            ->first();

        $rows = collect();

        if ($version) {
            $pureTotal = $version->ingredients->sum(function ($ing) {
                return $ing->drops * ($ing->dilution / 100);
            });

            $rows = $version->ingredients->map(function ($ing) use ($pureTotal) {
                $pure = $ing->drops * ($ing->dilution / 100);
                $pct = $pureTotal > 0 ? ($pure / $pureTotal) * 100 : 0;

                return [
                    'material_id' => $ing->material_id,
                    'material_name' => $ing->material->name,
                    'drops' => (string) $ing->drops,
                    'dilution' => $ing->dilution.'%',
                    'pure_pct' => number_format($pct, 2).'%',
                ];
            });
        }

        return view('blends.show', compact('blend', 'version', 'rows'));
    }
}

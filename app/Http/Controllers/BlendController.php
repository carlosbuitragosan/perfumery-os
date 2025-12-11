<?php

namespace App\Http\Controllers;

use App\Models\Blend;
use App\Models\Material;
use Illuminate\Http\Request;

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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $blend = Blend::create([
            'user_id' => auth()->id(),
            'name' => trim($data['name']),
        ]);

        return redirect()->route('blends.show', $blend);
    }

    public function show(Blend $blend)
    {
        abort_unless($blend->user_id === auth()->id(), 404);

        return view('blends.show', compact('blend'));
    }
}

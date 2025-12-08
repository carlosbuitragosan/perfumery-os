<?php

namespace App\Http\Controllers;

use App\Models\Material;

class BlendController extends Controller
{
    public function create()
    {
        $materials = Material::where('user_id', auth()->id())->orderBy('name')->get();

        return view('blends.create', compact('materials'));
    }
}

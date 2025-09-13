<?php

namespace App\Http\Controllers;

use App\Models\Material;

class BottleController extends Controller
{
    public function create(Material $material)
    {
        return view('bottles.create', compact('material'));
    }
}

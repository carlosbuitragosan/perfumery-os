<?php

namespace App\Http\Controllers;

use App\Models\Blend;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $blends = Blend::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', [
            'blends' => $blends,
        ]);
    }
}

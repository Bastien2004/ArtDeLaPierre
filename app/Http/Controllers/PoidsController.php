<?php

namespace App\Http\Controllers;

use App\Models\Poids;
use Illuminate\Http\Request;

class PoidsController extends Controller
{
    public function index()
    {
        $materiaux = Poids::orderBy('epaisseurCM', 'asc')->get();
        return view('poids.poids', compact('materiaux'));
    }
}

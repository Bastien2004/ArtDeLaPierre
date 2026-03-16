<?php
namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
class EmailController extends Controller
{
    // Appelé en AJAX pour l'autocomplete
    public function search(Request $request)
    {
        $q = $request->query('q', '');
        $emails = Email::where('adresse', 'like', '%' . $q . '%')
            ->orderBy('adresse')
            ->limit(10)
            ->pluck('adresse');

        return response()->json($emails);
    }

    // Enregistre un email s'il n'existe pas déjà
    public function store(Request $request)
    {
        $request->validate(['adresse' => 'required|email']);
        Email::firstOrCreate(['adresse' => $request->adresse]);
        return response()->json(['ok' => true]);
    }
}

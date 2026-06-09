<?php
namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
class EmailController extends Controller
{

    public function index()
    {
        $emails = \App\Models\Email::orderBy('created_at', 'desc')->get();
        return view('emails.registreMail', compact('emails')); // ← ici
    }

    public function update(Request $request, $id)
    {
        $request->validate(['adresse' => 'required|email']);

        $email = Email::findOrFail($id);
        $email->update([
            'adresse' => $request->adresse
        ]);

        return redirect()->route('emails.index')->with('success', 'Email mis à jour avec succès.');
    }

    public function destroy($id)
    {
        \App\Models\Email::findOrFail($id)->delete();
        return redirect()->route('emails.index')->with('success', 'Email supprimé.');
    }

    public function search(Request $request)
    {
        $q = $request->query('q', '');
        $emails = Email::where('adresse', 'like', '%' . $q . '%')
            ->orderBy('adresse')
            ->limit(10)
            ->pluck('adresse');

        return response()->json($emails);
    }

    public function store(Request $request)
    {
        $request->validate(['adresse' => 'required|email']);
        Email::firstOrCreate(['adresse' => $request->adresse]);
        return response()->json(['ok' => true]);
    }

    public function storeFromRegistre(Request $request)
    {
        $request->validate([
            'adresse' => 'required|email|unique:emails,adresse'
        ]);

        Email::create([
            'adresse' => $request->adresse
        ]);

        return redirect()->route('emails.index')->with('success', 'Nouvelle adresse ajoutée au carnet.');
    }
}

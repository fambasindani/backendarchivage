<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emplacement;


class EmplacementController extends Controller
{
    


public function getAllemplacement()
{
    return Emplacement::where('statut', 1)
                    ->orderBy('id', 'desc')
                    ->get(); // renvoie un tableau brut
}


   public function getAll()
{
    // Obtenir les emplacements actifs (statut = 1), triés du plus récent au plus ancien
    return Emplacement::where('statut', 1)
                      ->orderBy('id', 'desc') // ou 'created_at' si disponible
                      ->paginate(10);
}

public function searchEmplacement(Request $request)
{
    // Recherche des emplacements actifs avec filtre par nom
    $query = Emplacement::where('statut', 1);

    if ($request->has('search')) {
        $query->where('nom_emplacement', 'like', '%' . $request->search . '%');
    }

    return $query->orderBy('id', 'desc') // tri des résultats
                 ->paginate(10);
}


    public function addEmplacement(Request $request)
    {
        $request->validate([
            'nom_emplacement' => 'required|string|max:255|unique:emplacements',
        ]);

        Emplacement::create([
            'nom_emplacement' => $request->nom_emplacement,
            'statut' => 1,
        ]);

        return response()->json(['message' => 'Emplacement ajouté avec succès'], 201);
    }

    public function editEmplacement($id)
    {
        return Emplacement::findOrFail($id);
    }

    public function updateEmplacement(Request $request, $id)
    {
        $emplacement = Emplacement::findOrFail($id);

        $request->validate([
            'nom_emplacement' => 'required|string|max:255|unique:emplacements,nom_emplacement,' . $emplacement->id,
        ]);

        $emplacement->update($request->all());
        return response()->json(['message' => 'Emplacement mis à jour avec succès']);
    }

    public function supprimerEmplacement($id)
    {
        $emplacement = Emplacement::findOrFail($id);
        $emplacement->update(['statut' => 0]);
        return response()->json(['message' => 'Statut mis à jour avec succès']);
    }


























}

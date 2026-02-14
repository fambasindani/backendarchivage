<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Direction;



class DirectionController extends Controller
{

public function getAlldirection()
{
    return Direction::where('statut', 1)
                   ->orderBy('id', 'desc')
                    ->get(); // renvoie un tableau brut
}



   public function Getdirection()
{
    // 🔄 Récupération paginée des directions actives triées par ID décroissant
    return Direction::
                    orderBy('id', 'desc') // Tri par ordre d'insertion (les plus récentes d'abord)
                    ->paginate(10);
}


    // 🔍 Rechercher une direction par nom
    public function searchdirection(Request $request)
    {
        $search = $request->input('search');

        return Direction::where('nom', 'like', "%{$search}%")
                        ->where('statut', 1)
                        ->paginate(10);
    }

    // ➕ Ajouter une nouvelle direction
    public function createdirection(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:directions',
        ]);

        Direction::create([
            'nom' => $request->nom,
            'statut' => 1,
        ]);

        return response()->json(['message' => 'Direction créée avec succès']);
    }

    // 🔍 Récupérer une direction spécifique
    public function editdirection($id)
    {
        return Direction::findOrFail($id);
    }

    // 📝 Mettre à jour une direction
    public function updatedirection(Request $request, $id)
    {
        $direction = Direction::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255|unique:directions,nom,' . $direction->id,
        ]);

        $direction->update(['nom' => $request->nom]);

        return response()->json(['message' => 'Direction mise à jour avec succès']);
    }

    // ❌ Suppression logique de la direction
    public function deletedirection($id)
    {
        $direction = Direction::findOrFail($id);
        $direction->update(['statut' => 0]);

        return response()->json(['message' => 'Direction désactivée avec succès']);
    }


    






































}

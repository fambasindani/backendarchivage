<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classeur;

class ClasseurController extends Controller
{
    
public function getAllclasseur()
{
    return Classeur::where('statut', 1)
                    ->orderBy('id', 'desc')
                    ->get(); // renvoie un tableau brut
}

public function getAll()
{
    // Récupérer tous les classeurs avec statut = 1 triés par ID décroissant
    return Classeur::
                   orderBy('id', 'desc') // les plus récents en premier
                   ->paginate(10);
}


// Rechercher des classeurs par nom avec statut = 1
public function searchClasseur(Request $request)
{
    $query = Classeur::where('statut', 1); // on applique le filtre dès le début

    if ($request->has('search')) {
        $query->where('nom_classeur', 'like', '%' . $request->search . '%');
    }

    return $query->paginate(10);
}

    // Ajouter un nouveau classeur
    public function addClasseur(Request $request)
{
    $request->validate([
        'nom_classeur' => 'required|string|max:255|unique:classeurs', // Vérifie l'unicité
        'statut' => 'nullable|boolean', // Validation pour le statut
    ]);

    Classeur::create([
        'nom_classeur' => $request->nom_classeur,
        'statut' => "1", // Statut par défaut
    ]);

    return response()->json(['message' => 'Classeur ajouté avec succès'], 201);
}

     // Mettre à jour un classeur
public function updateClasseur(Request $request, $id)
{
    $classeur = Classeur::findOrFail($id);

    $request->validate([
        'nom_classeur' => 'required|string|max:255|unique:classeurs,nom_classeur,' . $classeur->id,
        // On ignore 'statut' car il sera forcé à 1
    ]);

    $classeur->nom_classeur = $request->nom_classeur;
    $classeur->statut = "1"; // Statut forcé à 1
    $classeur->save();

    return response()->json(['message' => 'Classeur mis à jour avec succès'.$request->nom_classeur]);
}



    // Éditer un classeur existant
    public function editClasseur($id)
    {
        return Classeur::findOrFail($id);
    }

   

public function supprimerClasseur($id)
{
    $classeur = Classeur::findOrFail($id);

    $classeur->statut = 0; // On force le statut à 0
    $classeur->save();

    return response()->json(['message' => 'Statut mis à jour avec succès']);
}



















}

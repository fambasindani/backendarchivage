<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CentreOrdonnancement;
use Carbon\Carbon;


class CentreOrdonnancementController extends Controller
{

public function getAll()
{
    // Récupérer tous les enregistrements où statut = 1
    // Les plus récents (insertions) apparaissent en premier grâce au tri par ID décroissant
    return CentreOrdonnancement::with('articleBudgetaire')
                                 -> where('statut', 1)
                                ->orderBy('id', 'desc') // tri décroissant
                                ->paginate(10);
}


public function getcentre()
{
    // Récupérer tous les enregistrements où statut = 1
    // Les plus récents (insertions) apparaissent en premier grâce au tri par ID décroissant
    return CentreOrdonnancement:: where('statut', 1)
                                ->orderBy('id', 'desc') // tri décroissant
                                ->paginate(10);
}


public function searchcentre(Request $request)
{
    // Recherche avec statut = 1
    $query = CentreOrdonnancement::with('articleBudgetaire')
    ->where('statut', 1);

    if ($request->has('search')) {
        $query->where('nom', 'like', '%' . $request->search . '%');
    }

    return $query->paginate(10);
}



public function addcentre(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255|unique:centre_ordonnancements', // Validation unique
        'description' => 'required|string|max:255',
        'id_ministere' => 'required|integer', // Validation requise
    ], [
        'id_ministere.required' => 'Veuillez sélectionner le champ nom du ministère.',
        'id_ministere.integer' => 'Veuillez sélectionner le champ nom du ministère.',
    ]);

    // Créer un nouvel enregistrement dans la table
    CentreOrdonnancement::create([
        'nom' => $request->nom,
        'description' => $request->description,
        'statut' => "1",
        'id_ministere' => $request->id_ministere, // Ajout du champ id_ministere
    ]);

    return response()->json(['message' => 'Centre ordonnancement ajouté avec succès']);
}






public function updateCentre(Request $request, $id)
{
    // 🧩 Récupération du centre
    $centre = CentreOrdonnancement::findOrFail($id);

    // ✅ Validation des données
    $request->validate([
        'nom' => 'required|string|max:255', // Validation unique
        'description' => 'required|string|max:255',
        'id_ministere' => 'required|integer', // Validation requise
    ], [
        'id_ministere.required' => 'Veuillez sélectionner le champ nom du ministère.',
        'id_ministere.integer' => 'Veuillez sélectionner le champ nom du ministère.',
    ]);

    // 🛠 Mise à jour des champs spécifiques
    $centre->nom = $request->nom;
    $centre->description = $request->description;
    $centre->id_ministere = $request->id_ministere;
    $centre->save();

    // 📦 Réponse JSON
     return response()->json(['message' => 'Centre ordonnancement mis à jour avec succès']);
}


    public function editcentre($id)
    {
        return CentreOrdonnancement::findOrFail($id);
    }

    

   public function supprimercentre($id)
    {
        $centreOrdonnancement = CentreOrdonnancement::findOrFail($id);
        
        // Met à jour le statut à '0' au lieu de supprimer
        $centreOrdonnancement->update(['statut' => '0']);
        
        return response()->json(['message' => 'Statut mis à jour avec succès']);
    }





















    
}

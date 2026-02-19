<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\NotePerception;

class NotePerceptionController extends Controller
{
    // 📄 Liste paginée avec les relations
    public function getNote()
    {
        return NotePerception::with(['classeur', 'centre', 'assujetti', 'emplacement', 'utilisateur', 'ArticleBudgetaire'])
                             ->where('statut', 1)
                              ->orderBy('id', 'desc')
                             ->paginate(10);
    }

public function getNote_centre($id)
{
    return NotePerception::with([
        'classeur',
        'centre',
        'assujetti',
        'emplacement',
        'utilisateur',
        'articlebudgetaire'
    ])
    ->where('statut', 1)
    ->where('id_centre_ordonnancement', $id)
    ->paginate(10);
}


    // 🔍 Recherche sur les relations (nom_classeur, nom_centre, nom_emplacement, nom_assujetti)
    public function searchnote(Request $request)
    {
        $search = $request->input('search');

        return NotePerception::with(['classeur', 'centre', 'assujetti', 'emplacement', 'utilisateur', "ArticleBudgetaire"])
            ->where('statut', 1)
            ->where(function ($query) use ($search) {
                $query->whereHas('classeur', function ($q) use ($search) {
                    $q->where('nom_classeur', 'like', "%{$search}%");
                })->orWhereHas('centre', function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%");
                })->orWhereHas('emplacement', function ($q) use ($search) {
                    $q->where('nom_emplacement', 'like', "%{$search}%");
                })->orWhereHas('assujetti', function ($q) use ($search) {
                    $q->where('nom_raison_sociale', 'like', "%{$search}%");
                });
            })
            ->paginate(10);
    }




  public function searchnote_id(Request $request, $id)
{
    $search = $request->input('search');

    return NotePerception::with(['classeur', 'centre', 'assujetti', 'emplacement', 'utilisateur', 'ArticleBudgetaire'])
        ->where('statut', 1)
        ->where('id_centre_ordonnancement', $id) // Ajout de la condition pour filtrer par id_ministere
        ->where(function ($query) use ($search) {
            $query->whereHas('classeur', function ($q) use ($search) {
                $q->where('nom_classeur', 'like', "%{$search}%");
            })->orWhereHas('centre', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%");
            })->orWhereHas('emplacement', function ($q) use ($search) {
                $q->where('nom_emplacement', 'like', "%{$search}%");
            })->orWhereHas('assujetti', function ($q) use ($search) {
                $q->where('nom_raison_sociale', 'like', "%{$search}%");
            });
        })
        ->paginate(10);
}




public function searchnote_idcentre(Request $request, $id)
{
    $search = $request->input('search');

    return NotePerception::with([
        'classeur',
        'centre',
        'assujetti',
        'emplacement',
        'utilisateur',
        'articlebudgetaire'
    ])
    ->where('statut', 1)
    ->where('id_centre_ordonnancement', $id) // ✅ correction ici
    ->where(function ($query) use ($search) {
        $query->whereHas('classeur', function ($q) use ($search) {
            $q->where('nom_classeur', 'like', "%{$search}%");
        })->orWhereHas('centre', function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%"); // ✅ correction ici
        })->orWhereHas('emplacement', function ($q) use ($search) {
            $q->where('nom_emplacement', 'like', "%{$search}%");
        })->orWhereHas('assujetti', function ($q) use ($search) {
            $q->where('nom_raison_sociale', 'like', "%{$search}%");
        });
    })
    ->paginate(10);
}






    // ➕ Création d’une note
    public function createnote(Request $request)
    {
 $request->validate([
    'id_ministere' => 'required|integer',
    'numero_serie' => 'required|string|max:255',
    'date_ordonnancement' => 'required|date',
    'date_enregistrement' => 'required|date',
    'id_classeur' => 'required|integer',
    'id_user' => 'required|integer',
    'id_centre_ordonnancement' => 'required|integer',
    'id_assujetti' => 'required|integer',
    'id_emplacement' => 'required|integer',
], [
    'id_ministere.required' => 'Le champ nom du ministère est obligatoire.',
    'id_ministere.integer' => 'Le champ nom du ministère doit être un nombre entier.',
]);

        NotePerception::create($request->all());

        return response()->json(['message' => 'Note enregistrée avec succès']);
    }

    // 🔍 Lecture d’une note spécifique
    public function  editnote($id)
    {
        return NotePerception::with(['classeur', 'centre', 'assujetti', 'emplacement', 'utilisateur'])
                             ->findOrFail($id);
    }

    // 📝 Mise à jour
    public function note(Request $request, $id)
    {
        $note = NotePerception::findOrFail($id);

        $request->validate([
            'id_ministere'=> 'required|integer',
            'numero_serie' => 'required|string|max:255',
            'date_ordonnancement' => 'required|date',
            'date_enregistrement' => 'required|date',
            'id_classeur'=> 'required|integer',
            'id_user'=> 'required|integer',
            'id_centre_ordonnancement'=> 'required|integer',
            'id_assujetti'=> 'required|integer',
            'id_emplacement'=> 'required|integer',
          


               
        ]);

        $note->update($request->all());

        return response()->json(['message' => 'Note modifiée avec succès']);
    }

    // ❌ Suppression logique
    public function deletenote($id)
    {
        $note = NotePerception::findOrFail($id);
        $note->update(['statut' => 0]);

        return response()->json(['message' => 'Note désactivée']);
    }
}

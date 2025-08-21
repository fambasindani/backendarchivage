<?php




namespace App\Http\Controllers;

use App\Models\Declaration;
use Illuminate\Http\Request;

class DeclarationController extends Controller
{





  public function editdeclaration($id)
    {
        return Declaration::with(['direction', 'emplacement'])->findOrFail($id);
    }


public function getDeclarations()
{
    $declarations = Declaration::with(['direction', 'emplacement', 'classeur'])
        ->where('statut', 1)
        ->orderBy('id', 'desc')
        ->paginate(20);

    $declarations->getCollection()->transform(function ($item) {
        $item->nom_direction = $item->direction->nom ?? null;
        $item->nom_emplacement = $item->emplacement->nom_emplacement?? null;
        return $item;
    });

    return response()->json($declarations);
}


public function searchDeclarations(Request $request)
{
    $search = $request->input('search');
    $page = $request->input('page', 1); // par défaut page 1 si rien envoyé

    $query = Declaration::with(['direction', 'emplacement','classeur'])
        ->where('statut', 1)
        ->where(function ($q) use ($search) {
            $q->where('intitule', 'like', "%$search%")
              ->orWhere('num_reference', 'like', "%$search%")
              ->orWhere('mot_cle', 'like', "%$search%")
              ->orWhere('num_declaration', 'like', "%$search%");
        });

    $declarations = $query->orderBy('id', 'desc')->paginate(20, ['*'], 'page', $page);

    $declarations->getCollection()->transform(function ($item) {
        $item->nom_direction = $item->direction->nom ?? null;
        $item->nom_emplacement = $item->emplacement->nom_emplacement ?? null;
        return $item;
    });

    return response()->json($declarations);
}


    // ➕ Création
    public function createdeclaration(Request $request)
    {
        $validated = $request->validate([
            'id_direction' => 'required|integer',
            'id_emplacement' => 'required|integer',
            'id_classeur' => 'required|integer',
            'id_user' => 'required|integer',
            'date_creation' => 'required|date',
            'date_enregistrement' => 'required|date',
            'intitule' => 'required|string|max:255',
            'num_reference' => 'required|string|max:255',
            'mot_cle' => 'required|string|max:255',
            'num_declaration' => 'required|string|max:255',
        ]);

        $validated['statut'] = 1;

        $declaration = Declaration::create($validated);

        return response()->json($declaration, 201);
    }

    // ✏️ Mise à jour
    public function updatedeclaration(Request $request, $id)
    {
        $declaration = Declaration::findOrFail($id);

        $validated = $request->validate([
            'id_direction' => 'required|integer',
            'id_emplacement' => 'required|integer',
            'id_classeur' => 'required|integer',
            'id_user' => 'required|integer',
            'date_creation' => 'required|date',
            'date_enregistrement' => 'required|date',
            'intitule' => 'required|string|max:255',
            'num_reference' => 'required|string|max:255',
            'mot_cle' => 'required|string|max:255',
            'num_declaration' => 'required|string|max:255',
        ]);

        $declaration->update($validated);

        return response()->json($declaration);
    }

   public function destroy($id)
{
    $declaration = Declaration::findOrFail($id);
    $declaration->update(['statut' => 0]);

    return response()->json(['message' => 'Déclaration désactivée avec succès']);
}

}

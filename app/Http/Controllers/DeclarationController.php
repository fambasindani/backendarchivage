<?php




namespace App\Http\Controllers;
use App\Models\Declaration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentDeclaration;

class DeclarationController extends Controller
{

public function editdeclaration($id)
    {
        return Declaration::with(['direction', 'emplacement'])->findOrFail($id);
    }


public function getlistedocument($id)
{
    $declarations = Declaration::with(['direction', 'emplacement', 'classeur'])
        ->where('statut', 1)
        ->where('id_classeur', $id) // Ajout de la condition where pour filtrer par id
        ->orderBy('id', 'desc')
        ->paginate(20);

    $declarations->getCollection()->transform(function ($item) {
        $item->nom_direction = $item->direction->nom ?? null;
        $item->nom_emplacement = $item->emplacement->nom_emplacement ?? null;
        return $item;
    });




    return response()->json($declarations);
}

public function searchDeclarationsfiltre(Request $request, $id)
{
    $search = $request->input('search');
    $page = $request->input('page', 1); // par défaut page 1

    $query = Declaration::with(['direction', 'emplacement', 'classeur'])
        ->where('statut', 1)
        ->where('id_classeur', $id)
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


public function listedocumentdirectionall(Request $request, $id)
{
    //$id = $request->input('id_classeur');
    $id_direction = $request->input('id_direction');

    $declarations = Declaration::with(['direction', 'emplacement', 'classeur'])
        ->where('statut', 1)
        ->where('id_classeur', $id)
        ->where('id_direction', $id_direction)
        ->orderBy('id', 'desc')
        ->paginate(20);

    $declarations->getCollection()->transform(function ($item) {
        $item->nom_direction = $item->direction->nom ?? null;
        $item->nom_emplacement = $item->emplacement->nom_emplacement ?? null;
        return $item;
    });

    return response()->json($declarations);
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
    // public function createdeclaration(Request $request)
    // {
    //     $validated = $request->validate([
    //         'id_direction' => 'required|integer',
    //         'id_emplacement' => 'required|integer',
    //         'id_classeur' => 'required|integer',
    //         'id_user' => 'required|integer',
    //         'date_creation' => 'required|date',
    //         'date_enregistrement' => 'required|date',
    //         'intitule' => 'required|string|max:255',
    //         'num_reference' => 'required|string|max:255',
    //         'mot_cle' => 'required|string|max:255',
    //         'num_declaration' => 'required|string|max:255',
    //     ]);

    //     $validated['statut'] = 1;

    //     $declaration = Declaration::create($validated);

    //     return response()->json($declaration, 201);
    // }




public function createdeclarations(Request $request)
{
    // 1️⃣ Validation de la déclaration
    $validatedDeclaration = $request->validate([
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

    $validatedDeclaration['statut'] = 1;

    // 2️⃣ Démarrage de la transaction
    DB::beginTransaction();

    try {
        // Création de la déclaration
        $declaration = Declaration::create($validatedDeclaration);

        $documents = [];

        if ($request->hasFile('files')) {
            // Validation des fichiers
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'file|mimes:pdf|max:51200', // max 50 Mo
            ]);

            foreach ($request->file('files') as $file) {
                $nomFichier = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $dossier = "document_declaration/" . ($request->id_classeur + 100);

                // Stockage du fichier
                $file->storeAs($dossier, $nomFichier);

                // Création du document lié à la déclaration
                $documents[] = DocumentDeclaration::create([
                    'id_declaration' => $declaration->id,
                    'id_classeur' => $request->id_classeur,
                    'nom_fichier' => $nomFichier,
                    'nom_native' => $file->getClientOriginalName(),
                ]);
            }
        }

        // ✅ Tout est bon, commit de la transaction
        DB::commit();

        return response()->json([
            'declaration' => $declaration,
            'documents' => $documents,
            'message' => 'Déclaration créée et fichiers uploadés avec succès ✅'
        ], 201);

    } catch (\Exception $e) {
        // ❌ Une erreur est survenue, rollback
        DB::rollBack();

        return response()->json([
            'message' => 'Erreur lors de la création de la déclaration ou de l’upload des fichiers',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function createdeclaration(Request $request)
{
    // 1️⃣ Validation de la déclaration seulement (sans les fichiers)
    $validatedDeclaration = $request->validate([
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

    $validatedDeclaration['statut'] = 1;

    try {
        // Création de la déclaration seulement
        $declaration = Declaration::create($validatedDeclaration);

        return response()->json([
            'success' => true,
            'id' => $declaration->id,
            'declaration' => $declaration,
            'message' => 'Déclaration créée avec succès ✅'
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création de la déclaration',
            'error' => $e->getMessage()
        ], 500);
    }
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

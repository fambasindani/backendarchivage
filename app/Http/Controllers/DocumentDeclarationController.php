<?php

namespace App\Http\Controllers;

use App\Models\DocumentDeclaration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentDeclarationController extends Controller
{

    
public function getallpdf($id_declaration)
{
    // 🔎 Récupère les documents liés à la déclaration avec l'ID donné
    return DocumentDeclaration::where('id_declaration', $id_declaration)
                              ->orderBy('id', 'desc') // facultatif, pour trier les plus récents d’abord
                              ->get();
}

public function deleteDocument($id)
{
    // 🔎 Récupère le document avec l'ID donné
    $document = DocumentDeclaration::find($id);

    // Vérifie si le document existe
    if (!$document) {
        return response()->json(['message' => 'Document non trouvé'], 404);
    }

    // Suppression du document
    $document->delete();

    // Retourne une réponse indiquant le succès de l'opération
    return response()->json(['message' => 'Document supprimé avec succès']);
}




    // 📤 Upload multiple PDF dans dossier par id_classeur + 100
    public function uploadMultiple(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf|max:51200', // max 50 Mo
            'id_declaration' => 'required|integer',
            'id_classeur' => 'required|integer',
        ]);

        $documents = [];

        foreach ($request->file('files') as $file) {
            $nomFichier = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $dossier = "document_declaration/" . ($request->id_classeur + 100);

            // Stockage dans le dossier spécifique
            $file->storeAs($dossier, $nomFichier);

            // 🛠️ Correction ici : on ajoute id_classeur dans le create()
            $documents[] = DocumentDeclaration::create([
                'id_declaration' => $request->id_declaration,
                'id_classeur' => $request->id_classeur, // ← Important !
                'nom_fichier' => $nomFichier,
                'nom_native' => $file->getClientOriginalName(),
            ]);
        }

        return response()->json([
            'message' => 'Fichiers PDF uploadés avec succès ✅',
            'documents' => $documents
        ], 201);
    }



    











    // 📥 Téléchargement du fichier PDF
    public function download($id)
{
    $document = DocumentDeclaration::findOrFail($id);

    $idClasseur = $document->id_classeur ?? null;

    if (!$idClasseur) {
        return response()->json(['error' => 'Classeur non défini ❌'], 400);
    }

    $dossier = "document_declaration/" . ($idClasseur + 100);
    $chemin = storage_path("app/{$dossier}/{$document->nom_fichier}");

    if (!file_exists($chemin)) {
        return response()->json(['error' => 'Fichier introuvable 📁'], 404);
    }

    return response()->file($chemin, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $document->nom_native . '"'
    ]);
}









}

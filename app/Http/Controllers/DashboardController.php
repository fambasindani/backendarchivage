<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   


/*  public function getDeclarationSummary()
{
    $summary = DB::table('declarations')
        ->join('classeurs', 'declarations.id_classeur', '=', 'classeurs.id')
        ->groupBy('classeurs.nom_classeur')
        ->orderByDesc(DB::raw('COUNT(declarations.id)'))
        ->pluck(DB::raw('COUNT(declarations.id) as total'), 'classeurs.nom_classeur')
        ->map(function ($total, $nom_classeur) {
            return [
                'nom_classeur' => $nom_classeur,
                'total' => $total
            ];
        })
        ->values();

    return response()->json($summary);
}
  */












public function getDeclarationSummary()
{
    $summary = DB::table('declarations')
        ->join('classeurs', 'declarations.id_classeur', '=', 'classeurs.id')
        ->select(
            'classeurs.id as id_classeur',
            'classeurs.nom_classeur',
            DB::raw('COUNT(declarations.id) as total'),
            DB::raw('MAX(declarations.created_at) as last_created_at')
        )
        ->groupBy('classeurs.id', 'classeurs.nom_classeur')
        ->orderByDesc('total')
        ->get()
        ->map(function ($item) {
            return [
                'nom_classeur' => $item->nom_classeur,
                'id_classeur' => $item->id_classeur,
                'created_at' => $item->last_created_at,
                'total' => $item->total
            ];
        });

    return response()->json($summary);
}






    public function declarationSearch(Request $request)
    {
        $query = DB::table('declarations')
            ->join('classeurs', 'declarations.id_classeur', '=', 'classeurs.id')
            ->join('directions', 'declarations.id_direction', '=', 'directions.id')
            ->select('classeurs.nom_classeur', DB::raw('COUNT(declarations.id) as total'))
            ->groupBy('classeurs.nom_classeur');

        // 🔍 Filtrer par direction si fourni
        if ($request->has('direction_id')) {
            $query->where('declarations.id_direction', $request->direction_id);
        }

        // 🔍 Filtrer par classeur si fourni
        if ($request->has('classeur_id')) {
            $query->where('declarations.id_classeur', $request->classeur_id);
        }

        $summary = $query->orderBy('total', 'desc')->get();

        return response()->json($summary);
    }


 public function searchDeclaration(Request $request)
{
    $nom_classeur = $request->input('nom_classeur');
    $id_direction = $request->input('id_direction');

    $query = DB::table('declarations')
        ->join('classeurs', 'declarations.id_classeur', '=', 'classeurs.id')
        ->join('directions', 'declarations.id_direction', '=', 'directions.id')
        ->where('classeurs.nom_classeur', 'LIKE', "%{$nom_classeur}%")
        ->where('declarations.id_direction', $id_direction);

    $results = $query->select(
        'declarations.id',
        'classeurs.nom_classeur',
        'directions.nom',
        'declarations.created_at'
    )->get();

    $total = $query->count();

    return response()->json([
        'total' => $total,
        'declarations' => $results
    ]);
}






public function Searchdeclarationx(Request $request)
{
    $query = DB::table('declarations')
        ->join('classeurs', 'declarations.id_classeur', '=', 'classeurs.id')
        ->join('directions', 'declarations.id_direction', '=', 'directions.id')
        ->select(
            'declarations.id_direction', // ID de la direction
            'classeurs.nom_classeur',    // Nom du classeur
            'classeurs.id as id_classeur', // ID du classeur
            'directions.nom as nom_direction', // Nom de la direction
            DB::raw('COUNT(declarations.id) as total')
        )
        ->groupBy('declarations.id_direction', 'classeurs.nom_classeur', 'classeurs.id', 'directions.nom');

    // 🔍 Filtrer uniquement si direction_id et nom_classeur sont fournis
    if ($request->has('direction_id') && $request->has('nom_classeur')) {
        $query->where('declarations.id_direction', $request->direction_id)
              ->where('classeurs.nom_classeur', $request->nom_classeur);
    }

    $summary = $query->orderBy('total', 'desc')->get();

    return response()->json($summary);
}

/* {
  "direction_id": 2,
  "classeur_id": 17
} */









}

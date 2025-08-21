<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Compagnie;
use Illuminate\Support\Facades\Hash;

class UtilisateurController extends Controller
{
    // 🔄 Récupération paginée des utilisateurs actifs
    public function getUtilisateurs()
    {
        return Utilisateur::where('statut', 1)->paginate(10);
    }

    // 🔍 Rechercher un utilisateur par nom ou prénom
    public function searchUtilisateur(Request $request)
    {
        $search = $request->input('search');

        return Utilisateur::where(function($query) use ($search) {
                    $query->where('nom', 'like', "%{$search}%")
                          ->orWhere('prenom', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->where('statut', 1)
                ->paginate(10);
    }

    // ➕ Ajouter un nouvel utilisateur
    public function createUtilisateur(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
        ]);

        Utilisateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'statut' => 1,
        ]);

        return response()->json(['message' => 'Utilisateur créé avec succès']);
    }

    // 🔍 Récupérer un utilisateur spécifique
    public function editUtilisateur($id)
    {
        return Utilisateur::findOrFail($id);
    }

    // 📝 Mettre à jour un utilisateur
    public function updateUtilisateur(Request $request, $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email,' . $utilisateur->id,
            'role' => 'required|string',
        ]);

        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $utilisateur->update($data);

        return response()->json(['message' => 'Utilisateur mis à jour avec succès']);
    }

    // ❌ Suppression logique de l'utilisateur
    public function deleteUtilisateur($id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->update(['statut' => 0]);

        return response()->json(['message' => 'Utilisateur désactivé avec succès']);
    }




public function login(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // On charge les modules (qui contiennent la compagnie)
    $utilisateur = Utilisateur::with(['modules.compagnie'])
        ->where('email', $validated['email'])
        ->first();

    if (!$utilisateur || !Hash::check($validated['password'], $utilisateur->password)) {
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    $token = $utilisateur->createToken('token_utilisateur')->plainTextToken;

    // Récupérer la première compagnie via les modules (ou null si aucun)
    $compagnie = $utilisateur->modules->first()?->compagnie;

    return response()->json([
        'token' => $token,
        'utilisateur' => [
            'id' => $utilisateur->id,
            'nom' => $utilisateur->nom,
            'email' => $utilisateur->email,
            'compagnie' => $compagnie ? [
                'id' => $compagnie->id,
                'nom' => $compagnie->nom,
            ] : null,
            'modules' => $utilisateur->modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'nom' => $module->nom,
                    // ajoute d’autres champs si nécessaire
                ];
            }),
        ],
    ]);
}












}







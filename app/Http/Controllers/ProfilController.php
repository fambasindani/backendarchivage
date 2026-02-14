<?php

namespace App\Http\Controllers;

use App\Models\MonUtilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function monProfil(Request $request)
    {
        $utilisateur = $request->user()->load([
            'departements',
            'roles',
            'roles.permissions'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'full_name' => $utilisateur->full_name,
                'email' => $utilisateur->email,
                'statut' => $utilisateur->statut,
                'datecreation' => $utilisateur->datecreation,
                'dernierconnection' => $utilisateur->dernierconnection,
                
                'departements' => $utilisateur->departements->map(function($dept) {
                    return [
                        'id' => $dept->id,
                        'nom' => $dept->nom,
                        'sigle' => $dept->sigle,
                        'datecreation' => $dept->datecreation
                    ];
                }),
                
                'departement_principal' => $utilisateur->principal_departement ? [
                    'id' => $utilisateur->principal_departement->id,
                    'nom' => $utilisateur->principal_departement->nom,
                    'sigle' => $utilisateur->principal_departement->sigle
                ] : null,
                
                'roles' => $utilisateur->roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'nom' => $role->nom,
                        'permissions' => $role->permissions->pluck('code')
                    ];
                }),
                
                'permissions' => $utilisateur->roles->flatMap(function($role) {
                    return $role->permissions->pluck('code');
                })->unique()->values(),
                
                'created_at' => $utilisateur->created_at,
                'updated_at' => $utilisateur->updated_at
            ]
        ]);
    }

    /**
     * Afficher le profil d'un utilisateur spécifique
     */
    public function show($id)
    {
        $utilisateur = MonUtilisateur::with(['departements', 'roles'])->find($id);

        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'datecreation' => $utilisateur->created_at,
                'avatar' => $utilisateur->avatar,
                'full_name' => $utilisateur->full_name,
                'email' => $utilisateur->email,
                'statut' => $utilisateur->statut,
                'departements' => $utilisateur->departements,
                'roles' => $utilisateur->roles
            ]
        ]);
    }

    /**
     * Modifier le profil d'un utilisateur
     */
    public function modifierProfil(Request $request, $id)
    {
        $user = MonUtilisateur::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:50',
            'prenom' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|unique:monutilisateurs,email,' . $id,
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 🔹 Données à mettre à jour
            $updateData = [];

            if ($request->has('nom')) {
                $updateData['nom'] = $request->nom;
            }
            if ($request->has('prenom')) {
                $updateData['prenom'] = $request->prenom;
            }
            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // 🔥 GESTION DE L'IMAGE - DOSSIER app/avatar
            if ($request->hasFile('avatar')) {
                // Supprimer l'ancienne image
                if ($user->avatar) {
                    Storage::disk('local')->delete('avatar/' . $user->avatar);
                }
                
                // Générer un nom unique avec le format: id_timestamp.extension
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = $id . '_' . time() . '.' . $extension;
                
                // Stocker dans app/avatar
                $file->storeAs('avatar', $filename, 'local');
                
                // Sauvegarder le nom dans la BD
                $updateData['avatar'] = $filename;
            }

            // Mise à jour
            $user->update($updateData);
            
            // 🔥 Recharger l'utilisateur
            $user = MonUtilisateur::find($id);
            
            // 🔥 URL complète avec le prefix profil
            $user->avatar_url = $user->avatar 
                ? url('profil/avatar/' . $user->avatar)
                : null;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_url' => $user->avatar_url,
                    'statut' => $user->statut,
                    'updated_at' => $user->updated_at
                ],
                'message' => 'Profil mis à jour avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher le profil d'un utilisateur avec toutes ses relations
     */
    public function afficherProfil($id)
    {
        $user = MonUtilisateur::with(['roles', 'departements'])->find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        // 🔥 URL complète avec le prefix profil
        $user->avatar_url = $user->avatar 
            ? url('api/profil/avatar/' . $user->avatar)
            : null;
        
        // 🔥 Ajouter les initiales pour fallback
        $user->initiales = $this->getInitiales($user);
        
        // 🔥 Ajouter une couleur basée sur l'ID
        $user->couleur_avatar = $this->getCouleurAvatar($user->id);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Profil récupéré avec succès'
        ], 200);
    }

   /**
 * Servir les fichiers avatar depuis storage/app/avatar/
 */
public function getAvatar($filename)
{
    try {
        $path = storage_path('app/avatar/' . $filename);

        if (!file_exists($path)) {
            \Log::error('Avatar non trouvé', [
                'filename' => $filename,
                'path' => $path,
                'exists' => false
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Image non trouvée: ' . $filename
            ], 404);
        }

        return response()->file($path);

    } catch (\Exception $e) {

        \Log::error('Erreur lors de la récupération de l’avatar', [
            'filename' => $filename,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la récupération de l’image.'
        ], 500);
    }
}

















    // ===========================================
    // 🔥 MÉTHODES UTILITAIRES
    // ===========================================

    /**
     * Générer les initiales à partir du nom et prénom
     */
    private function getInitiales($user)
    {
        $initials = '';
        if ($user->prenom) {
            $initials .= mb_substr($user->prenom, 0, 1);
        }
        if ($user->nom) {
            $initials .= mb_substr($user->nom, 0, 1);
        }
        return strtoupper($initials);
    }

    /**
     * Générer une couleur pour l'avatar basée sur l'ID
     */
    private function getCouleurAvatar($id)
    {
        $colors = ['primary', 'success', 'warning', 'info', 'danger', 'purple', 'pink', 'indigo'];
        $index = crc32($id) % count($colors);
        return $colors[$index];
    }
}
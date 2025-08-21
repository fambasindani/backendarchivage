<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CentreOrdonnancementController;
use App\Http\Controllers\ClasseurController;
use App\Http\Controllers\EmplacementController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\AssujettiController;
use App\Http\Controllers\NotePerceptionController;
use App\Http\Controllers\DocumentNotePerceptionController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\DocumentDeclarationController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ArticleBudgetaireController;
use App\Http\Controllers\DashboardController;










//php artisan make:model DocumentDeclaration -mcr


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::middleware('auth:sanctum')->group(function () {



// controller pour Dashboard








// controller pour modules
Route::get('/modules', [ModuleController::class, 'Getmodule']);
Route::get('/modules/search', [ModuleController::class, 'searchmodule']);
Route::post('/modules', [ModuleController::class, 'createmodule']);
Route::get('/modules/{id}', [ModuleController::class, 'editemodule']);
Route::put('/modules/{id}', [ModuleController::class, 'updatemodule']);
Route::delete('/modules/{id}', [ModuleController::class, 'destroy']);



});

//controller pour le Dashboard
Route::get('/declaration-dashboard', [DashboardController::class, 'getDeclarationSummary']);
Route::post('/declaration-search', [DashboardController::class, 'Searchdeclaration']);


Route::post('/login', [UtilisateurController::class, 'login'])->name('login');

//controller utilisateur
Route::get('/utilisateurs', [UtilisateurController::class, 'getUtilisateurs']);
Route::post('/utilisateurs/search', [UtilisateurController::class, 'searchUtilisateur']);
Route::post('/utilisateurs', [UtilisateurController::class, 'createUtilisateur']);
Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'editUtilisateur']);
Route::put('/utilisateurs/{id}', [UtilisateurController::class, 'updateUtilisateur']);
Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'deleteUtilisateur']);






//document declarations

Route::post('/documents-declaration/upload-multiple', [DocumentDeclarationController::class, 'uploadMultiple']);
Route::get('/documents-declaration/download/{id}', [DocumentDeclarationController::class, 'download']);
Route::get('/documents/{id}', [DocumentDeclarationController::class, 'getallpdf']);
Route::delete('/delete-document/{id}', [DocumentDeclarationController::class, 'deleteDocument']);






//Controller pour  Declaration
Route::get('/declarations', [DeclarationController::class, 'getDeclarations']);
Route::post('/declarations/search', [DeclarationController::class, 'searchDeclarations']);
Route::post('/declarations', [DeclarationController::class, 'createdeclaration']);
Route::put('/declarations/{id}', [DeclarationController::class, 'updatedeclaration']);
Route::delete('/declarations/{id}', [DeclarationController::class, 'destroy']);
Route::get('/editdeclaration/{id}', [DeclarationController::class, 'editdeclaration']);


//controller pour document_noteperception
Route::post('/notes/upload', [DocumentNotePerceptionController::class, 'uploadMultiple']);
Route::get('/notes/downloads/{id}', [DocumentNotePerceptionController::class, 'download']);
Route::get('/notes/download/{id}', [DocumentNotePerceptionController::class, 'getallpdf']);
Route::delete('/notes/delete/{id}', [DocumentNotePerceptionController::class, 'deleteDocument']);







//controller pour centre ordonnancement
Route::get('centre_ordonnancements/all', [CentreOrdonnancementController::class, 'getAll']);
Route::post('centre_ordonnancements/search', [CentreOrdonnancementController::class, 'searchcentre']);
Route::post('centre_ordonnancements', [CentreOrdonnancementController::class, 'addcentre']);
Route::get('centre_ordonnancements/{id}', [CentreOrdonnancementController::class, 'editcentre']);
Route::put('centre_ordonnancements/{id}', [CentreOrdonnancementController::class, 'updatecentre']);
Route::delete('centre_ordonnancements/{id}', [CentreOrdonnancementController::class, 'supprimercentre']);






//controller pour Classeur
Route::get('/classeurs', [ClasseurController::class, 'getAll']); // Récupérer tous les classeurs
Route::get('/classeurs/search', [ClasseurController::class, 'searchClasseur']); // Rechercher des classeurs
Route::post('/classeurs', [ClasseurController::class, 'addClasseur']); // Ajouter un nouveau classeur
Route::get('/classeurs/{id}', [ClasseurController::class, 'editClasseur']); // Éditer un classeur existant
Route::put('/classeurs/{id}', [ClasseurController::class, 'updateClasseur']); // Mettre à jour un classeur
Route::delete('/classeurs/{id}', [ClasseurController::class, 'supprimerClasseur']); // Supprimer un classeur (mettre à jour le statut)
Route::get('/classeur', [ClasseurController::class, 'getAllclasseur']);




//controller pour Emplacement
Route::get('/emplacements', [EmplacementController::class, 'getAll']);
Route::get('/emplacements/search', [EmplacementController::class, 'searchEmplacement']);
Route::post('/emplacements', [EmplacementController::class, 'addEmplacement']);
Route::get('/emplacements/{id}', [EmplacementController::class, 'editEmplacement']);
Route::put('/emplacements/{id}', [EmplacementController::class, 'updateEmplacement']);
Route::delete('/emplacements/{id}', [EmplacementController::class, 'supprimerEmplacement']);
Route::get('/emplacement', [EmplacementController::class, 'getAllemplacement']);






//controller pour la direction
Route::get('/directions', [DirectionController::class, 'Getdirection']);
Route::post('/directions/search', [DirectionController::class, 'searchdirection']);
Route::get('/directions/{id}', [DirectionController::class, 'editdirection']);
Route::post('/directions', [DirectionController::class, 'createdirection']);
Route::put('/directions/{id}', [DirectionController::class, 'updatedirection']);
Route::delete('/directions/{id}', [DirectionController::class, 'deletedirection']);
Route::get('/direction', [DirectionController::class, 'getAlldirection']);




//controller pour le assujetti



Route::get('/assujettis', [AssujettiController::class, 'getassujetti']);
Route::get('/assujettis/search', [AssujettiController::class, 'searchassujetti']);
Route::post('/assujettis', [AssujettiController::class, 'createassujetti']);
Route::get('/assujettis/{id}', [AssujettiController::class, 'editassujetti']);
Route::put('/assujettis/{id}', [AssujettiController::class, 'updateassujetti']);
Route::delete('/assujettis/{id}', [AssujettiController::class, 'supprimerassujetti']);







//controller pour les notes

Route::get('/notes', [NotePerceptionController::class, 'getNote']);
Route::get('/notes/search', [NotePerceptionController::class, 'searchnote']);
Route::post('/notes', [NotePerceptionController::class, 'createnote']);
Route::get('/notes/{id}', [NotePerceptionController::class, 'editnote']);
Route::put('/notes/{id}', [NotePerceptionController::class, 'note']); // mise à jour
Route::delete('/notes/{id}', [NotePerceptionController::class, 'deletenote']);




//article budgetaire
Route::get('/article', [ArticleBudgetaireController::class, 'getArticle']); // Liste paginée
Route::post('/search-article', [ArticleBudgetaireController::class, 'searchArticle']); // Recherche
Route::post('/create-article', [ArticleBudgetaireController::class, 'creerArticle']); // Création
Route::get('/edit-article/{id}', [ArticleBudgetaireController::class, 'editArticle']); // Lecture d’un article
Route::put('/update-article/{id}', [ArticleBudgetaireController::class, 'updateArticle']); // Mise à jour
Route::delete('delete-article/{id}', [ArticleBudgetaireController::class, 'deleteArticle']); // Désactivation


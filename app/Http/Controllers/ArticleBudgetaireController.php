<?php




namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArticleBudgetaire;

class ArticleBudgetaireController extends Controller
{
    public function getArticle()
    {
        return ArticleBudgetaire::where('statut', 1)->paginate(10);
    }

    public function searchArticle(Request $request)
    {
        $search = $request->input('search');

        return ArticleBudgetaire::where('nom', 'like', "%{$search}%")
                                ->where('statut', 1)
                                ->paginate(10);
    }

public function creerArticle(Request $request)
{
    $request->validate([
        'article_budgetaire' => 'required|string|max:255|unique:article_budgetaires,article_budgetaire',
        'nom' => 'required|string|max:255|unique:article_budgetaires,nom',
    ]);

    ArticleBudgetaire::create($request->all());

    return response()->json(['message' => 'Article budgétaire ajouté avec succès']);
}

    public function editArticle($id)
    {
        return ArticleBudgetaire::findOrFail($id);
    }

    
public function updateArticle(Request $request, $id)
{
    $article = ArticleBudgetaire::findOrFail($id);

    $request->validate([
        'article_budgetaire' => 'required|string|max:255|unique:article_budgetaires,article_budgetaire,' . $id,
        'nom' => 'required|string|max:255|unique:article_budgetaires,nom,' . $id,
    ]);

    $article->update($request->all());

    return response()->json(['message' => 'Article budgétaire mis à jour avec succès']);
}


    public function deleteArticle($id)
    {
        $article = ArticleBudgetaire::findOrFail($id);
        $article->update(['statut' => 0]);

        return response()->json(['message' => 'Article budgétaire désactivé']);
    }
}

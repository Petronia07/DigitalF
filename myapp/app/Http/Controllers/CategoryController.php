<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé. Seul un administrateur peut créer des catégories.'], 403);
        }
    
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        // Créer la catégorie si l'utilisateur est admin
        $category = Category::create([
            'name' => $request->name,
        ]);
    
        return response()->json(['message' => 'Catégorie créée avec succès', 'data' => $category]);
    }

    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'message' => 'Liste des catégories récupérée avec succès',
            'data' => $categories
        ], 200);
    }

    public function delete($id)
    {
        // Vérification si la catégorie existe
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            return response()->json([
                'message' => "Catégorie supprimée avec succès",
            ]);
        } else {
            return response()->json(['message' => "Catégorie non trouvée"], 404);
        }
    }
}

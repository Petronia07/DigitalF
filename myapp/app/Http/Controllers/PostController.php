<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function create(Request $request)
    {
        // Validation des données
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        // Enregistrer l'image si elle est fournie
        $imagePath = null;
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts_images', 'public');
            $imageUrl = asset('storage/' . $imagePath);
        }

        // Créer le post
        $post = Post::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id, // ID de la catégorie
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
        ]);

        return response()->json(['message' => 'Post créé avec succès', 'post' => $post,  'image_url' => $imageUrl,], 201);
    }

    //Listes des Posts par catégories
    public function list()
    {
        $user_id = Auth::user()->id;

        // Récupérer les Posts de l'utilisateur connecté avec les catégories associées
        $posts = Post::with('category')->where('user_id', $user_id)->get();

        // Organiser les Posts par catégories
        $groupedByCategory = $posts->groupBy('category.name');

        return response()->json([
            'message' => "Liste des Posts par catégories",
            'data' => $groupedByCategory,
        ]);
    }

    //détails de chaque Post
    public function show($id)
    {
        $user_id = Auth::user()->id;
        //vérifions si le Post appartient à l'utilisateur connecté
        if (Post::where(['id' => $id, 'user_id' => $user_id])->exists()) {
            //retournons le Post
            $post = Post::where(['id' => $id, 'user_id' => $user_id])->get();
            return response()->json([
                'message' => "detail du Post",
                'data' => $post,
            ]);
        } else {
            return response()->json(['message' => "Post non trouvé"], 404);
        }
    }

    //mise à jour
    public function update(Request $request, $id)
    {
        $user_id = Auth::user()->id;

        $post = Post::where(['id' => $id, 'user_id' => $user_id])->first();

        if (!$post) {
            return response()->json(['message' => 'Post non trouvé'], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($request->hasFile('image')) {

            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            
            $imagePath = $request->file('image')->store('posts_images', 'public');
            $post->image = $imagePath;
        }

        $post->category_id = $request->category_id;
        $post->title = $request->title;
        $post->content = $request->content;

        $post->save();

        return response()->json(['message' => 'Données mises à jour avec succès', 'post' => $post]);
    }

    //Supprimer un post
    public function delete($id)
    {
        //récupération de l'utilisateur connecté
        $user_id = Auth::user()->id;

        if (Post::where(['id' => $id, 'user_id' => $user_id])->exists()) {
            //retournons le projet
            $post = Post::where(['id' => $id, 'user_id' => $user_id])->first();
            $post->delete();
            return response()->json([
                'message' => "Post supprimé avec succès",
            ]);
        } else {
            return response()->json(['message' => "Post non trouvé"], 404);
        }
    }
}

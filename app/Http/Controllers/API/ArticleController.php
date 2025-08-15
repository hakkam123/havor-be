<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::with(['industry', 'service']);

        if ($request->has('industry_id')) {
            $query->where('industry_id', $request->industry_id);
        }

        if ($request->has('services_id')) {
            $query->where('services_id', $request->services_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest()->paginate(10);

        foreach ($articles as $article) {
            $article->image_url = url('storage/' . $article->image_url);
        }

        return response()->json($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'industry_id' => 'required|exists:industries,id',
            'services_id' => 'required|exists:services,id'
        ]);

        $article = Article::create($validated);
        return response()->json($article->load(['industry', 'service']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $article = Article::with(['industry', 'service'])->findOrFail($id);
        $article->image_url = url('storage/' . $article->image_url);
        return response()->json($article);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'short_description' => 'sometimes|string',
            'content' => 'sometimes|string',
            'image_url' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'industry_id' => 'sometimes|exists:industries,id',
            'services_id' => 'sometimes|exists:services,id'
        ]);

        $article->update($validated);
        return response()->json($article->load(['industry', 'service']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(['message' => 'Article deleted successfully']);
    }
}

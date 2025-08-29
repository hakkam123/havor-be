<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Article;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    public function index()
    {
        $industries = Industry::withCount('articles')->get();
        return response()->json($industries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:industries'
        ]);

        $industry = Industry::create($validated);
        return response()->json($industry, 201);
    }

    public function show(Industry $industry)
    {
        return response()->json($industry->load('articles'));
    }

    public function update(Request $request, Industry $industry)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:industries,title,' . $industry->id
        ]);

        $industry->update($validated);
        return response()->json($industry);
    }

    public function destroy(Industry $industry)
    {
        if ($industry->articles()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete industry that has articles'
            ], 400);
        }

        $industry->delete();
        return response()->json(['message' => 'Industry deleted successfully']);
    }

    public function articles($id)
    {
        $industry = Industry::findOrFail($id);
        
        $articles = Article::with(['industry', 'service'])
            ->where('industry_id', $id)
            ->latest()
            ->paginate(10);

        foreach ($articles as $article) {
            $article->image_url = url('storage/' . $article->image_url);
        }

        return response()->json([
            'industry' => $industry,
            'articles' => $articles
        ]);
    }
}

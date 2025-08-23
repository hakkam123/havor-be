<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Industry;
use App\Models\Service;
use App\Http\Helpers\FileUploadHelper;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['industry', 'service'])->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $industries = Industry::all();
        $services = Service::all();
        return view('admin.articles.create', compact('industries', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'author' => 'required|string|max:255',
            'industry_id' => 'required|exists:industries,id',
            'services_id' => 'required|exists:services,id',
        ]);

        if ($request->hasFile('image')) {
            $imageErrors = FileUploadHelper::validateImageFile($request->file('image'));
            if (!empty($imageErrors)) {
                return back()->withErrors(['image' => $imageErrors])->withInput();
            }
            $validated['image_url'] = FileUploadHelper::uploadImage($request->file('image'), 'articles/images');
        }

        unset($validated['image']);

        Article::create($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully.');
    }

    public function show(Article $article)
    {
        $article->load(['industry', 'service']);
        return view('admin.articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $industries = Industry::all();
        $services = Service::all();
        return view('admin.articles.edit', compact('article', 'industries', 'services'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'author' => 'required|string|max:255',
            'industry_id' => 'required|exists:industries,id',
            'services_id' => 'required|exists:services,id',
        ]);

        if ($request->hasFile('image')) {
            $imageErrors = FileUploadHelper::validateImageFile($request->file('image'));
            if (!empty($imageErrors)) {
                return back()->withErrors(['image' => $imageErrors])->withInput();
            }
            
            if ($article->image_url) {
                FileUploadHelper::deleteImage($article->image_url);
            }
            
            $validated['image_url'] = FileUploadHelper::uploadImage($request->file('image'), 'articles/images');
        }

        unset($validated['image']);

        $article->update($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article)
    {
        if ($article->image_url) {
            FileUploadHelper::deleteImage($article->image_url);
        }
        
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully.');
    }
}

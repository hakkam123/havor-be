<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Helpers\FileUploadHelper;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'features' => 'required|string',
            'price' => 'required|numeric|min:0',
            'hero_image' => 'required|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'content_image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'category' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            $imageErrors = FileUploadHelper::validateImageFile($request->file('hero_image'));
            if (!empty($imageErrors)) {
                return back()->withErrors(['hero_image' => $imageErrors])->withInput();
            }
            $validated['hero_image_url'] = FileUploadHelper::uploadImage($request->file('hero_image'), 'products/hero');
        }

        // Handle content image upload
        if ($request->hasFile('content_image')) {
            $imageErrors = FileUploadHelper::validateImageFile($request->file('content_image'));
            if (!empty($imageErrors)) {
                return back()->withErrors(['content_image' => $imageErrors])->withInput();
            }
            $validated['content_image_url'] = FileUploadHelper::uploadImage($request->file('content_image'), 'products/content');
        }

        // Remove file inputs from validated data
        unset($validated['hero_image'], $validated['content_image']);

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'features' => 'required|string',
            'price' => 'required|numeric|min:0',
            'hero_image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'content_image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'category' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            $imageErrors = FileUploadHelper::validateImageFile($request->file('hero_image'));
            if (!empty($imageErrors)) {
                return back()->withErrors(['hero_image' => $imageErrors])->withInput();
            }
            
            // Delete old hero image if exists
            if ($product->hero_image_url) {
                FileUploadHelper::deleteImage($product->hero_image_url);
            }
            
            $validated['hero_image_url'] = FileUploadHelper::uploadImage($request->file('hero_image'), 'products/hero');
        }

        // Handle content image upload
        if ($request->hasFile('content_image')) {
            $imageErrors = FileUploadHelper::validateImageFile($request->file('content_image'));
            if (!empty($imageErrors)) {
                return back()->withErrors(['content_image' => $imageErrors])->withInput();
            }
            
            // Delete old content image if exists
            if ($product->content_image_url) {
                FileUploadHelper::deleteImage($product->content_image_url);
            }
            
            $validated['content_image_url'] = FileUploadHelper::uploadImage($request->file('content_image'), 'products/content');
        }

        // Remove file inputs from validated data
        unset($validated['hero_image'], $validated['content_image']);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete associated image files
        if ($product->hero_image_url) {
            FileUploadHelper::deleteImage($product->hero_image_url);
        }
        if ($product->content_image_url) {
            FileUploadHelper::deleteImage($product->content_image_url);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}

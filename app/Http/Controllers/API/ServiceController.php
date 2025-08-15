<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Helpers\FileUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->get();
        
        foreach ($services as $service) {
            if ($service->icon_url) {
                $service->icon_url = url('storage/' . $service->icon_url);
            }
            if ($service->hero_image) {
                $service->hero_image = url('storage/' . $service->hero_image);
            }
        }
        
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', 
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'features' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'hero_image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
        ]);

        if ($request->hasFile('icon')) {
            $iconErrors = FileUploadHelper::validateImageFile($request->file('icon'));
            if (!empty($iconErrors)) {
                return response()->json(['errors' => ['icon' => $iconErrors]], 422);
            }
            $validated['icon_url'] = FileUploadHelper::uploadImage($request->file('icon'), 'services/icons');
        }

        if ($request->hasFile('hero_image')) {
            $heroErrors = FileUploadHelper::validateImageFile($request->file('hero_image'));
            if (!empty($heroErrors)) {
                return response()->json(['errors' => ['hero_image' => $heroErrors]], 422);
            }
            $validated['hero_image'] = FileUploadHelper::uploadImage($request->file('hero_image'), 'services/hero');
        }

        $service = Service::create($validated);

        return response()->json($service, 201);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        
        if ($service->icon_url) {
            $service->icon_url = url('storage/' . $service->icon_url);
        }
        if ($service->hero_image) {
            $service->hero_image = url('storage/' . $service->hero_image);
        }
        
        return response()->json($service);
    }

    public function projects($id)
    {
        $service = Service::findOrFail($id);
        $projects = $service->projects()->latest()->get();

        foreach ($projects as $project) {
            if ($project->image_url) {
                $project->image_url = url('storage/' . $project->image_url);
            }
        }

        return response()->json([
            'message' => [ 
                'success' => true,
                'service' => [
                                'id' => $service->id,
                                'name' => $service->name,
                                'short_description' => $service->short_description,
                            ],
                'projects' => $projects
            ],
            
        ]);
    }

    public function articles($id)
    {
        $service = Service::findOrFail($id);
        $articles = $service->articles()->latest()->get();
        
        foreach ($articles as $article) {
            if ($article->image_url) {
                $article->image_url = url('storage/' . $article->image_url);
            }
        }
        
        return response()->json([
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'short_description' => $service->short_description,
            ],
            'articles' => $articles
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'short_description' => 'sometimes|string|max:500',
            'features' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'hero_image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
        ]);

        if ($request->hasFile('icon')) {
            $iconErrors = FileUploadHelper::validateImageFile($request->file('icon'));
            if (!empty($iconErrors)) {
                return response()->json(['errors' => ['icon' => $iconErrors]], 422);
            }

            if ($service->icon_url) {
                FileUploadHelper::deleteImage($service->icon_url);
            }

            $validated['icon_url'] = FileUploadHelper::uploadImage($request->file('icon'), 'services/icons');
        }

        if ($request->hasFile('hero_image')) {
            $heroErrors = FileUploadHelper::validateImageFile($request->file('hero_image'));
            if (!empty($heroErrors)) {
                return response()->json(['errors' => ['hero_image' => $heroErrors]], 422);
            }

            if ($service->hero_image) {
                FileUploadHelper::deleteImage($service->hero_image);
            }

            $validated['hero_image'] = FileUploadHelper::uploadImage($request->file('hero_image'), 'services/hero');
        }

        $service->update($validated);

        return response()->json($service);
    }

    public function destroy(Service $service)
    {
        if ($service->icon_url) {
            FileUploadHelper::deleteImage($service->icon_url);
        }

        if ($service->hero_image) {
            FileUploadHelper::deleteImage($service->hero_image);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}

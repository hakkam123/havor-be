<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Project;
use App\Http\Helpers\FileUploadHelper;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
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
                return back()->withErrors(['icon' => $iconErrors])->withInput();
            }
            $validated['icon_url'] = FileUploadHelper::uploadImage($request->file('icon'), 'services/icons');
        }

        if ($request->hasFile('hero_image')) {
            $heroErrors = FileUploadHelper::validateImageFile($request->file('hero_image'));
            if (!empty($heroErrors)) {
                return back()->withErrors(['hero_image' => $heroErrors])->withInput();
            }
            $validated['hero_image'] = FileUploadHelper::uploadImage($request->file('hero_image'), 'services/hero');
        }

        $service = Service::create($validated);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        $service->load('projects');
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
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
                return back()->withErrors(['icon' => $iconErrors])->withInput();
            }
            
            if ($service->icon_url) {
                FileUploadHelper::deleteImage($service->icon_url);
            }
            
            $validated['icon_url'] = FileUploadHelper::uploadImage($request->file('icon'), 'services/icons');
        }

        if ($request->hasFile('hero_image')) {
            $heroErrors = FileUploadHelper::validateImageFile($request->file('hero_image'));
            if (!empty($heroErrors)) {
                return back()->withErrors(['hero_image' => $heroErrors])->withInput();
            }
            
            if ($service->hero_image) {
                FileUploadHelper::deleteImage($service->hero_image);
            }
            
            $validated['hero_image'] = FileUploadHelper::uploadImage($request->file('hero_image'), 'services/hero');
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Service updated successfully.');
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

        return redirect()->route('admin.services.index')
                         ->with('success', 'Service deleted successfully.');
    }
}

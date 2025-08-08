<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Helpers\FileUploadHelper;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        // Sorting bisa disesuaikan dengan kolom lain, misalnya 'name' atau 'price'
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
            'features' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'icon' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
        ]);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $iconErrors = FileUploadHelper::validateImageFile($request->file('icon'));
            if (!empty($iconErrors)) {
                return back()->withErrors(['icon' => $iconErrors])->withInput();
            }
            $validated['icon_url'] = FileUploadHelper::uploadImage($request->file('icon'), 'services/icons');
        }

        Service::create($validated);

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
            'features' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'icon' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
        ]);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $iconErrors = FileUploadHelper::validateImageFile($request->file('icon'));
            if (!empty($iconErrors)) {
                return back()->withErrors(['icon' => $iconErrors])->withInput();
            }
            
            // Delete old icon if exists
            if ($service->icon_url) {
                FileUploadHelper::deleteImage($service->icon_url);
            }
            
            $validated['icon_url'] = FileUploadHelper::uploadImage($request->file('icon'), 'services/icons');
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        // Delete associated icon file
        if ($service->icon_url) {
            FileUploadHelper::deleteImage($service->icon_url);
        }
        
        $service->delete();

        return redirect()->route('admin.services.index')
                         ->with('success', 'Service deleted successfully.');
    }
}

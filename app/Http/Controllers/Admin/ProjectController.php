<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use App\Models\Clients;
use App\Http\Helpers\FileUploadHelper;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['service', 'client'])->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $services = Service::all();
        $clients = Clients::all();
        return view('admin.projects.create', compact('services', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'project_date' => 'required|date',
            'status' => 'required|in:planning,in_progress,completed',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageErrors = FileUploadHelper::validateImageFile($request->file('image'));
            if (!empty($imageErrors)) {
                return back()->withErrors(['image' => $imageErrors])->withInput();
            }
            $validated['image_url'] = FileUploadHelper::uploadImage($request->file('image'), 'projects/images');
        }

        // Set client_name from selected client
        $client = Clients::find($request->client_id);
        $validated['client_name'] = $client->title;

        Project::create($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['service', 'client']);
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $services = Service::all();
        $clients = Clients::all();
        return view('admin.projects.edit', compact('project', 'services', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048),
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'project_date' => 'required|date',
            'status' => 'required|in:planning,in_progress,completed',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageErrors = FileUploadHelper::validateImageFile($request->file('image'));
            if (!empty($imageErrors)) {
                return back()->withErrors(['image' => $imageErrors])->withInput();
            }
            
            // Delete old image if exists
            if ($project->image_url) {
                FileUploadHelper::deleteImage($project->image_url);
            }
            
            $validated['image_url'] = FileUploadHelper::uploadImage($request->file('image'), 'projects/images');
        }

        // Set client_name from selected client
        $client = Clients::find($request->client_id);
        $validated['client_name'] = $client->title;

        $project->update($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        // Delete associated image file
        if ($project->image_url) {
            FileUploadHelper::deleteImage($project->image_url);
        }
        
        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}

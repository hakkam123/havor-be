<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Clients;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with(['services', 'client'])->latest()->get();
        foreach ($projects as $project) {
            $project->image_url = url('storage/' . $project->image_url);
            if ($project->client) {
                $project->client->icon_url = url('storage/' . $project->client->icon_url);
            }
        }
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'project_date' => 'required|date',
            'status' => 'sometimes|in:planning,in_progress,completed'
        ]);

        $client = Clients::find($validated['client_id']);
        $validated['client_name'] = $client->title;

        $project = Project::create($validated);

        return response()->json($project->load(['service', 'client']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $project = Project::with(['services', 'client'])->findOrFail($id);
        $project->image_url = url('storage/' . $project->image_url);
        if ($project->client) {
            $project->client->icon_url = url('storage/' . $project->client->icon_url);
        }
        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'content' => 'sometimes|string',
            'image_url' => 'sometimes|string|max:255',
            'client_id' => 'sometimes|exists:clients,id',
            'service_id' => 'sometimes|exists:services,id',
            'project_date' => 'sometimes|date',
            'status' => 'sometimes|in:planning,in_progress,completed'
        ]);

        if (isset($validated['client_id'])) {
            $client = Clients::find($validated['client_id']);
            $validated['client_name'] = $client->title;
        }

        $project->update($validated);
        return response()->json($project->load(['service', 'client']));
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }
}

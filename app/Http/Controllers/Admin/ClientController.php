<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Http\Helpers\FileUploadHelper;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Clients::withCount('projects')->paginate(10);
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048)
        ]);

        if ($request->hasFile('icon')) {
            $iconErrors = FileUploadHelper::validateImageFile($request->file('icon'));
            if (!empty($iconErrors)) {
                return back()->withErrors(['icon' => $iconErrors])->withInput();
            }
            $validated['icon_url'] = FileUploadHelper::uploadImage($request->file('icon'), 'clients/icons');
        }

        Clients::create($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Clients $client)
    {
        $client->load(['projects.services']);
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Clients $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Clients $client)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:' . env('APP_UPLOAD_MAX_SIZE', 2048)
        ]);

        if ($request->hasFile('icon')) {
            $iconErrors = FileUploadHelper::validateImageFile($request->file('icon'));
            if (!empty($iconErrors)) {
                return back()->withErrors(['icon' => $iconErrors])->withInput();
            }
            
            if ($client->icon_url) {
                FileUploadHelper::deleteImage($client->icon_url);
            }
            
            $validated['icon_url'] = FileUploadHelper::uploadImage($request->file('icon'), 'clients/icons');
        }

        $client->update($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Clients $client)
    {
        if ($client->icon_url) {
            FileUploadHelper::deleteImage($client->icon_url);
        }
        
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}

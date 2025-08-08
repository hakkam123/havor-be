<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use Illuminate\Http\Request;

class ClientContoller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Clients::latest()->get();
        return response()->json($clients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_url' => 'required|string|max:255'
        ]);

        $client = Clients::create($validated);

        return response()->json($client, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Clients $client)
    {
        return response()->json($client->load('projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Clients $client)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'icon_url' => 'sometimes|string|max:255'
        ]);

        $client->update($validated);
        return response()->json($client);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clients $client)
    {
        $client->delete();
        return response()->json(['message' => 'Client deleted successfully']);
    }
}

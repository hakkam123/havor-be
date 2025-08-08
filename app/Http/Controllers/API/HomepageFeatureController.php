<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HomepageFeature;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomepageFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = HomepageFeature::orderBy('order_index')->get();
        return response()->json($features);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_url' => 'required|string|max:255',
            'order_index' => 'required|integer|unique:homepage_features'
        ]);

        $feature = HomepageFeature::create($validated);
        return response()->json($feature, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(HomepageFeature $homepageFeature)
    {
        return response()->json($homepageFeature);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomepageFeature $homepageFeature)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'icon_url' => 'sometimes|string|max:255',
            'order_index' => ['sometimes', 'integer', Rule::unique('homepage_features')->ignore($homepageFeature->id)]
        ]);

        $homepageFeature->update($validated);
        return response()->json($homepageFeature);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomepageFeature $homepageFeature)
    {
        $homepageFeature->delete();
        return response()->json(['message' => 'Homepage feature deleted successfully']);
    }
}

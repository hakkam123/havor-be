<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageFeature;
use Illuminate\Http\Request;

class HomepageFeatureController extends Controller
{
    public function index()
    {
        $features = HomepageFeature::paginate(10);
        return view('admin.homepage-features.index', compact('features'));
    }

    public function create()
    {
        return view('admin.homepage-features.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        HomepageFeature::create($data);

        return redirect()->route('admin.homepage-features.index')
            ->with('success', 'Homepage feature created successfully.');
    }

    public function show(HomepageFeature $homepageFeature)
    {
        return view('admin.homepage-features.show', compact('homepageFeature'));
    }

    public function edit(HomepageFeature $homepageFeature)
    {
        return view('admin.homepage-features.edit', compact('homepageFeature'));
    }

    public function update(Request $request, HomepageFeature $homepageFeature)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $homepageFeature->update($data);

        return redirect()->route('admin.homepage-features.index')
            ->with('success', 'Homepage feature updated successfully.');
    }

    public function destroy(HomepageFeature $homepageFeature)
    {
        $homepageFeature->delete();

        return redirect()->route('admin.homepage-features.index')
            ->with('success', 'Homepage feature deleted successfully.');
    }
}

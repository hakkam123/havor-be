<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    public function index()
    {
        $industries = Industry::withCount(['articles'])->paginate(10);
        return view('admin.industries.index', compact('industries'));
    }

    public function create()
    {
        return view('admin.industries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        Industry::create($request->all());

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industry created successfully.');
    }

    public function show(Industry $industry)
    {
        $industry->load(['articles']);
        return view('admin.industries.show', compact('industry'));
    }

    public function edit(Industry $industry)
    {
        return view('admin.industries.edit', compact('industry'));
    }

    public function update(Request $request, Industry $industry)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        $industry->update($request->all());

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industry updated successfully.');
    }

    public function destroy(Industry $industry)
    {
        if ($industry->articles()->count() > 0) {
            return redirect()->route('admin.industries.index')
                ->with('error', 'Cannot delete industry that has articles.');
        }

        $industry->delete();

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industry deleted successfully.');
    }
}

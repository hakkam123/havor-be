<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\FileUploadHelper;

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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'icon' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:2048',
            ]);

            // Handle file upload dan map ke database field
            if ($request->hasFile('icon')) {
                $iconErrors = FileUploadHelper::validateImageFile($request->file('icon'));
                if (!empty($iconErrors)) {
                    return back()->withErrors(['icon' => $iconErrors])->withInput();
                }

                // Upload file dan get path
                $iconPath = FileUploadHelper::uploadImage($request->file('icon'), 'industries/icons');
                
                // Replace file object dengan path string untuk database
                $validated['icon'] = $iconPath;
            } else {
                // Remove icon field jika tidak ada file
                unset($validated['icon']);
            }

            $industry = Industry::create($validated);

            return redirect()->route('admin.industries.index')
                ->with('success', 'Industry created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create industry: ' . $e->getMessage())
                ->withInput();
        }
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:2048', 
        ]);

        if ($request->hasFile('icon')) {
            $iconErrors = FileUploadHelper::validateImageFile($request->file('icon'));
            if (!empty($iconErrors)) {
                return back()->withErrors(['icon' => $iconErrors])->withInput();
            }

            if ($industry->icon) {
                FileUploadHelper::deleteImage($industry->icon);
            }

            $validated['icon'] = FileUploadHelper::uploadImage($request->file('icon'), 'industries/icons');
        }

        $industry->update($validated);

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industry updated successfully.');
    }

    public function destroy(Industry $industry)
    {
        if ($industry->articles()->count() > 0) {
            return redirect()->route('admin.industries.index')
                ->with('error', 'Cannot delete industry that has articles.');
        }

        if ($industry->icon) {
            FileUploadHelper::deleteImage($industry->icon);
        }

        $industry->delete();

        return redirect()->route('admin.industries.index')
            ->with('success', 'Industry deleted successfully.');
    }
}

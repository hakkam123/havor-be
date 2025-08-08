<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $leads = $query->latest()->paginate(10);
        return view('admin.leads.index', compact('leads'));
    }

    public function show(Lead $lead)
    {
        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        return view('admin.leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,qualified,converted,closed',
            'notes' => 'nullable|string',
        ]);

        $lead->update($request->only(['status', 'notes']));

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'leads' => 'required|array',
            'leads.*' => 'exists:leads,id',
            'status' => 'required|in:new,contacted,qualified,converted,closed',
        ]);

        Lead::whereIn('id', $request->leads)->update(['status' => $request->status]);

        return redirect()->route('admin.leads.index')
            ->with('success', 'Leads updated successfully.');
    }
}

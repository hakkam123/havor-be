@extends('layouts.admin')

@section('title', 'Projects')
@section('page-title', 'Projects Management')

@section('page-actions')
  <div class="flex items-center justify-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Projects</h3>
    <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
      <i class="bi bi-plus-circle me-2"></i> Add New Project
    </a>
  </div>
@endsection

@section('content')

@if(session('success'))
  <div class="mb-4 rounded-md bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</div>
@endif

@if(session('error'))
  <div class="mb-4 rounded-md bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg card-shadow p-8">
  <div class="flex items-center justify-between mb-3">
    <h3 class="mb-0 fw-bold text-dark"></h3>
    <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
        Add New Project
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b">
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:40px;">No.</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="min-width:180px;">Title</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="min-width:150px;">Client</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="min-width:120px;">Service</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="min-width:120px;">Project Date</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="min-width:100px;">Status</th>
          <th class="py-2 text-center text-xs font-semibold text-slate-500" style="width:120px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($projects as $project)
          <tr class=" hover:bg-slate-50 transition">
            <td class="py-3">{{ $project->id }}</td>
            <td class="py-3 font-medium text-slate-700">{{ $project->title }}</td>
            <td class="py-3">
              @if($project->client)
                <span class="font-medium text-slate-700">{{ $project->client->title }}</span>
              @else
                <span class="text-slate-400">{{ $project->client_name ?? 'No Client' }}</span>
              @endif
            </td>
            <td class="py-3 text-slate-500">{{ $project->service->title ?? 'N/A' }}</td>
            <td class="py-3 text-slate-500">{{ \Carbon\Carbon::parse($project->project_date)->format('M d, Y') }}</td>
            <td class="py-3">
              @php
                $statusBg = $project->status === 'completed' ? 'emerald-100' : ($project->status === 'in_progress' ? 'amber-100' : 'slate-100');
                $statusText = $project->status === 'completed' ? 'emerald-700' : ($project->status === 'in_progress' ? 'amber-700' : 'slate-600');
              @endphp
              <span class="inline-block px-2 py-0.5 rounded bg-{{ $statusBg }} text-{{ $statusText }} text-xs font-semibold">
                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
              </span>
            </td>
            <td class="py-3 text-center">
              <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-amber-500 bg-white hover:bg-amber-50 me-1" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center px-2 py-1 border rounded text-red-600 bg-white hover:bg-red-50" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-8 text-slate-400">
              <i class="bi bi-folder-x text-3xl mb-2"></i>
              <div>No projects found</div>
              <a href="{{ route('admin.projects.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
                <i class="bi bi-plus-circle me-2"></i> Add First Project
              </a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($projects->hasPages())
    <div class="mt-4 flex justify-end">
      {{ $projects->links() }}
    </div>
  @endif
</div>
@endsection
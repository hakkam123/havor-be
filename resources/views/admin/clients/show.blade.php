@extends('layouts.admin')

@section('title', $client->title)
@section('page-title', 'Clients Management')

@section('page-actions')
  <div class="flex items-center justify-end gap-2 mb-6">
    <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">
      <i class="bi bi-arrow-left"></i> Back to Clients
    </a>
  </div>
@endsection
<style>
  .custom-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
  }
  .custom-modal.show {
    display: flex !important;
  }
  .custom-modal-dialog {
    background: white;
    border-radius: 0.5rem;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    overflow: hidden;
  }
  .custom-modal-header,
  .custom-modal-footer {
    padding: 1rem;
    border-bottom: 1px solid #eee;
  }
  .custom-modal-footer {
    border-top: none;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
  }
  .custom-modal-body {
    padding: 1rem;
  }
  .custom-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    float: right;
    cursor: pointer;
    color: #999;
  }
  .custom-close:hover {
    color: #333;
  }
</style>

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
  <div class="lg:col-span-2 space-y-8">
    <div class="bg-white rounded-lg card-shadow p-8">
      <div class="flex flex-col md:flex-row gap-8 items-center">
        <div class="flex flex-col items-center justify-center w-full md:w-auto">
          @if($client->icon_url)
            <img src="{{ asset('storage/' . $client->icon_url) }}" alt="{{ $client->title }}" class="rounded bg-slate-100 shadow h-48 w-72 object-cover mb-4">
          @else
            <div class="bg-slate-100 rounded flex items-center justify-center h-48 w-72 mb-4">
              <i class="bi bi-building text-slate-300 text-5xl"></i>
            </div>
          @endif
        </div>
        <div class="flex-1">
          <h2 class="font-bold text-2xl text-slate-700 mb-1">{{ $client->title }}</h2>
          <p class="text-slate-500 mb-4">{{ $client->description }}</p>
          <div class="grid grid-cols-2 gap-6">
            <div>
              <span class="text-xs text-slate-500 font-semibold">Total Projects</span><br>
              <span class="inline-block px-3 py-1 rounded bg-sky-100 text-sky-700 text-lg font-bold mt-1">{{ $client->projects->count() }}</span>
            </div>
            <div>
              <span class="text-xs text-slate-500 font-semibold">Client Since</span><br>
              <span class="text-slate-700 text-sm mt-1">{{ $client->created_at->format('M d, Y') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg card-shadow p-8">
      <div class="flex items-center justify-between mb-6">
        <h3 class="font-semibold text-xl text-slate-800">Client Projects</h3>
        <a href="{{ route('admin.projects.create') }}?client_id={{ $client->id }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
          Add Project
        </a>
      </div>
      @if($client->projects->count() > 0)
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b text-slate-500">
                <th class="py-2 text-left">Name</th>
                <th class="py-2 text-left">Service</th>
                <th class="py-2 text-left">Status</th>
                <th class="py-2 text-left">Created</th>
                <th class="py-2 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($client->projects as $project)
              <tr class="border-b hover:bg-slate-50 transition">
                <td class="py-3">
                  <div class="flex items-center gap-3">
                    @if($project->image_url)
                      <img src="{{ asset('storage/' . $project->image_url) }}" alt="{{ $project->title }}" class="rounded bg-slate-100 border h-10 w-10 object-cover">
                    @else
                      <div class="bg-slate-100 border rounded flex items-center justify-center h-10 w-10">
                        <i class="bi bi-folder text-slate-300 text-xl"></i>
                      </div>
                    @endif
                    <div>
                      <strong class="text-slate-700">{{ $project->title }}</strong>
                      @if($project->description)
                        <br><span class="text-xs text-slate-400">{{ Str::limit($project->description, 50) }}</span>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="py-3">
                  @if($project->service)
                    <span class="inline-block px-2 py-0.5 rounded bg-sky-100 text-sky-700 text-xs font-semibold">{{ $project->service->name }}</span>
                  @else
                    <span class="text-slate-400 text-xs">-</span>
                  @endif
                </td>
                <td class="py-3">
                  @if($project->project_url)
                    <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs font-semibold">Live</span>
                  @else
                    <span class="inline-block px-2 py-0.5 rounded bg-amber-100 text-amber-700 text-xs font-semibold">In Progress</span>
                  @endif
                </td>
                <td class="py-3 text-slate-500">
                  <span class="text-xs">{{ $project->created_at->format('M d, Y') }}</span>
                </td>
                <td class="py-3 text-center">
                  <a href="{{ route('admin.projects.show', $project) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-sky-600 bg-white hover:bg-sky-50 me-1" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-amber-500 bg-white hover:bg-amber-50" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center py-8">
          <i class="bi bi-folder-x text-slate-300 text-4xl mb-2"></i>
          <h5 class="text-slate-400 mb-1">No Projects Yet</h5>
          <p class="text-slate-400 mb-3">This client doesn't have any projects associated yet.</p>
        </div>
      @endif
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-white rounded-lg card-shadow p-6">
      <h5 class="font-semibold text-slate-700 mb-4">Client Details</h5>
      <div class="mb-3">
        <span class="text-xs text-slate-500 font-semibold">Created</span><br>
        <span class="text-slate-700 text-sm mt-1">{{ $client->created_at->format('F d, Y \a\t H:i') }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-500 font-semibold">Last Updated</span><br>
        <span class="text-slate-700 text-sm mt-1">{{ $client->updated_at->format('F d, Y \a\t H:i') }}</span>
      </div>
    </div>

    <div class="bg-white rounded-lg card-shadow p-6">
      <h5 class="font-semibold text-slate-700 mb-4">Quick Actions</h5>
      <div class="flex flex-col gap-3">
        <a href="{{ route('admin.clients.edit', $client) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-400 text-white rounded shadow hover:bg-yellow-500 transition">
          <i class="bi bi-pencil"></i> Edit Client
        </a>
        <button type="button" id="openDeleteModal" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded shadow hover:bg-red-200 transition">
          <i class="bi bi-trash"></i> Delete Client
        </button>
      </div>
    </div>
  </div>
</div>

<div class="custom-modal" id="deleteModal" aria-hidden="true">
  <div class="custom-modal-dialog">
    <div class="custom-modal-content">
      <div class="custom-modal-header">
        <button type="button" class="custom-close" data-close-modal>&times;</button>
      </div>
      <div class="custom-modal-body">
        <p>Are you sure you want to delete <strong>{{ $client->title }}</strong>?</p>
        @if($client->projects->count() > 0)
          <div class="rounded bg-amber-50 text-amber-900 p-3 mb-3 text-sm">
            <i class="bi bi-exclamation-triangle"></i>
            This client has {{ $client->projects->count() }} associated project(s). Deleting this client will also remove the client reference from those projects.
          </div>
        @endif
        <p class="text-slate-400">This action cannot be undone.</p>
      </div>
      <div class="custom-modal-footer">
        <button type="button" class="bg-slate-100 text-slate-700 px-4 py-2 rounded hover:bg-slate-200 transition" data-close-modal>
          Cancel
        </button>
        <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
            Delete Client
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('deleteModal');
    const openBtn = document.getElementById('openDeleteModal');
    const closeBtns = modal.querySelectorAll('[data-close-modal]');

    openBtn.addEventListener('click', () => {
      modal.classList.add('show');
      modal.removeAttribute('aria-hidden');
    });

    closeBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
      });
    });

    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
      }
    });
  });
</script>

@endsection
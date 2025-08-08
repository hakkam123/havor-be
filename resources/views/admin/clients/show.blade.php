@extends('layouts.admin')

@section('title', $client->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $client->title }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Clients
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        @if($client->icon_url)
                            <img src="{{ $client->icon_url }}" alt="{{ $client->title }}" 
                                 class="rounded border mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-light border rounded d-flex align-items-center justify-content-center mb-3" 
                                 style="width: 120px; height: 120px; margin: 0 auto;">
                                <i class="bi bi-building text-muted fs-1"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h3>{{ $client->title }}</h3>
                        <p class="text-muted mb-3">{{ $client->description }}</p>
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Total Projects:</strong><br>
                                <span class="badge bg-primary fs-6">{{ $client->projects->count() }}</span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Client Since:</strong><br>
                                <span class="text-muted">{{ $client->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($client->projects->count() > 0)
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Client Projects ({{ $client->projects->count() }})</h5>
                <a href="{{ route('admin.projects.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Add Project
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->projects as $project)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($project->image_url)
                                            <img src="{{ $project->image_url }}" alt="{{ $project->title }}" 
                                                 class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light border rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-folder text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $project->title }}</strong>
                                            @if($project->description)
                                                <br><small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($project->service)
                                        <span class="badge bg-info">{{ $project->service->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($project->project_url)
                                        <span class="badge bg-success">Live</span>
                                    @else
                                        <span class="badge bg-warning">In Progress</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $project->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="card mt-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-folder-x text-muted fs-1 mb-3"></i>
                <h5 class="text-muted">No Projects Yet</h5>
                <p class="text-muted">This client doesn't have any projects associated yet.</p>
                <a href="{{ route('admin.projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Create First Project
                </a>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Client Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Icon URL:</strong><br>
                    @if($client->icon_url)
                        <a href="{{ $client->icon_url }}" target="_blank" class="text-break">
                            {{ Str::limit($client->icon_url, 30) }}
                        </a>
                    @else
                        <span class="text-muted">No icon</span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <strong>Created:</strong><br>
                    <span class="text-muted">{{ $client->created_at->format('F d, Y \a\t H:i') }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Last Updated:</strong><br>
                    <span class="text-muted">{{ $client->updated_at->format('F d, Y \a\t H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    {{-- <a href="{{ route('admin.projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Add New Project
                    </a> --}}
                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil"></i> Edit Client
                    </a>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash"></i> Delete Client
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $client->title }}</strong>?</p>
                @if($client->projects->count() > 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        This client has {{ $client->projects->count() }} associated project(s). Deleting this client will also remove the client reference from those projects.
                    </div>
                @endif
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Client</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

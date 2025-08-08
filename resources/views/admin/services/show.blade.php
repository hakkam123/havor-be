@extends('layouts.admin')

@section('title', $service->title . ' - Havor Admin')
@section('page-title', 'Service: ' . $service->title)

@section('page-actions')
    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit Service
    </a>
    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Services
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-tools"></i> Service Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4>{{ $service->title }}</h4>
                        <p class="text-muted">{{ $service->description }}</p>
                        
                        <div class="mt-4">
                            <h6>Service Information:</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="120">Display Order:</th>
                                    <td><span class="badge bg-primary">{{ $service->order_index }}</span></td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $service->created_at->format('F d, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $service->updated_at->format('F d, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Icon URL:</th>
                                    <td>
                                        @if($service->icon_url)
                                            <a href="{{ $service->icon_url }}" target="_blank" class="text-decoration-none">
                                                {{ Str::limit($service->icon_url, 50) }}
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">No icon set</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        @if($service->icon_url)
                            <div class="mb-3">
                                <img src="{{ $service->icon_url }}" alt="Service Icon" 
                                     class="img-fluid border rounded" style="max-width: 150px;">
                            </div>
                        @else
                            <div class="mb-3">
                                <i class="bi bi-image text-muted" style="font-size: 6rem;"></i>
                                <p class="text-muted">No icon available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Projects -->
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-briefcase"></i> Related Projects ({{ $service->projects->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($service->projects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Project Title</th>
                                    <th>Client</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service->projects as $project)
                                    <tr>
                                        <td>{{ $project->title }}</td>
                                        <td>{{ $project->client_name }}</td>
                                        <td>{{ $project->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-briefcase text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-2">No Related Projects</h5>
                        <p class="text-muted">This service is not linked to any projects yet.</p>
                        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create Project
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-gear"></i> Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Service
                    </a>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Create New Service
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                        <i class="bi bi-list"></i> All Services
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="bi bi-bar-chart"></i> Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <h3 class="text-primary">{{ $service->projects->count() }}</h3>
                        <small class="text-muted">Linked Projects</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success">{{ $service->order_index }}</h5>
                        <small class="text-muted">Display Order</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-info">{{ $service->articles->count() }}</h5>
                        <small class="text-muted">Related Articles</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Danger Zone
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">Delete this service permanently. This action cannot be undone.</p>
                <form action="{{ route('admin.services.destroy', $service) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100" 
                            onclick="return confirm('Are you sure you want to delete this service? This action cannot be undone.')">
                        <i class="bi bi-trash"></i> Delete Service
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

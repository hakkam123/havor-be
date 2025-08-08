{{-- @extends('layouts.admin')

@section('title', 'Services - Havor Admin')
@section('page-title', 'Services Management')

@section('page-actions')
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Service
    </a>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-tools"></i> All Services
        </h6>
    </div>
    <div class="card-body">
        @if($services->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="80">Order</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th width="100">Icon</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $service->order_index }}</span>
                                </td>
                                <td>{{ $service->title }}</td>
                                <td>{{ Str::limit($service->description, 100) }}</td>
                                <td class="text-center">
                                    @if($service->icon_url)
                                        <img src="{{ $service->icon_url }}" alt="Icon" width="40" height="40" class="rounded">
                                    @else
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this service?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $services->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tools text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Services Found</h4>
                <p class="text-muted">Start by creating your first service.</p>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add First Service
                </a>
            </div>
        @endif
    </div>
</div>
@endsection --}}

@extends('layouts.admin')

@section('title', 'Services - Havor Admin')
@section('page-title', 'Services Management')

@section('page-actions')
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Service
    </a>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-tools"></i> All Services
        </h6>
    </div>
    <div class="card-body">
        @if($services->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Features</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>{{ $service->name }}</td>
                                <td>{{ Str::limit($service->description, 80) }}</td>
                                <td>{{ Str::limit($service->features, 80) }}</td>
                                <td>${{ number_format($service->price, 2) }}</td>
                                <td>{{ $service->duration ?? '-' }}</td>
                                <td>
                                    @if($service->is_featured)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a> --}}
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $services->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tools text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Services Found</h4>
                <p class="text-muted">Start by creating your first service.</p>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add First Service
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

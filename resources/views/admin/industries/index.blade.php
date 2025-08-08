@extends('layouts.admin')

@section('title', 'Industries')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Industries</h1>
    <a href="{{ route('admin.industries.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Industry
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Icon</th>
                        <th>Articles Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($industries as $industry)
                        <tr>
                            <td>{{ $industry->id }}</td>
                            <td>{{ $industry->name }}</td>
                            <td>{{ Str::limit($industry->description, 50) }}</td>
                            <td>
                                @if($industry->icon)
                                    <i class="{{ $industry->icon }}"></i>
                                @else
                                    <span class="text-muted">No icon</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $industry->articles_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.industries.show', $industry) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.industries.edit', $industry) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.industries.destroy', $industry) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this industry? This will also delete all related articles.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $industry->articles_count > 0 ? 'disabled title="Cannot delete industry with articles"' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-building-x fs-1"></i>
                                    <p class="mt-2">No industries found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($industries->hasPages())
            <div class="mt-4">
                {{ $industries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

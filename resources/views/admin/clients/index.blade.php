@extends('layouts.admin')

@section('title', 'Clients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Clients</h1>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Client
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
                        <th>No.</th>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Projects Count</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>
                                @if($client->icon_url)
                                    <img src="{{ $client->icon_url }}" alt="{{ $client->title }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-building text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $client->title }}</td>
                            <td>{{ Str::limit($client->description, 80) }}</td>
                            <td>
                                <span class="badge bg-info">{{ $client->projects_count }} projects</span>
                            </td>
                            <td>{{ $client->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this client?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-building-x fs-1"></i>
                                    <p class="mt-2">No clients found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="mt-4">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

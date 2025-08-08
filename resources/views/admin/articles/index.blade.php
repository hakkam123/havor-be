@extends('layouts.admin')

@section('title', 'Articles')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Articles</h1>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Article
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
                        <th>Title</th>
                        <th>Author</th>
                        <th>Industry</th>
                        <th>Service</th>
                        <th>Short Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $article->title }}</td>
                            <td>{{ $article->author }}</td>
                            <td>{{ $article->industry->name ?? 'N/A' }}</td>
                            <td>{{ $article->service->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($article->short_description, 50) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this article?')">
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
                                    <i class="bi bi-folder-x fs-1"></i>
                                    <p class="mt-2">No articles found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

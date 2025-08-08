@extends('layouts.admin')

@section('title', $article->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $article->title }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Articles
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3 text-muted">By {{ $article->author }}</h5>

                @if($article->image_url)
                    <img src="{{ $article->image_url }}" class="img-fluid rounded mb-4" alt="{{ $article->title }}">
                @endif

                <h6 class="text-muted">Short Description</h6>
                <p>{{ $article->short_description }}</p>

                <h6 class="text-muted">Full Content</h6>
                <div>{!! nl2br(e($article->content)) !!}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Industry:</strong><br>
                    <span>{{ $article->industry->name ?? 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <strong>Service:</strong><br>
                    <span>{{ $article->service->name ?? 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <strong>Published:</strong><br>
                    <span class="text-muted">{{ $article->created_at->format('F d, Y') }}</span>
                </div>
                <div class="mb-3">
                    <strong>Last Updated:</strong><br>
                    <span class="text-muted">{{ $article->updated_at->format('F d, Y \a\t H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil"></i> Edit Article
                </a>
                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete Article
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

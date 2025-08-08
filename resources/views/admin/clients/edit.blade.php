@extends('layouts.admin')

@section('title', 'Edit Client')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Client: {{ $client->title }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-primary">
            <i class="bi bi-eye"></i> View
        </a>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Clients
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.clients.update', $client) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Name *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $client->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required>{{ old('description', $client->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Client Icon</label>
                        <input type="file" class="form-control @error('icon') is-invalid @enderror" 
                               id="icon" name="icon" accept="image/*">
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Upload a new icon to replace the current one (JPEG, PNG, GIF, WebP, SVG - Max: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB)</div>
                        
                        @if($client->icon_url)
                            <div class="mt-2">
                                <label class="form-label">Current Icon:</label><br>
                                <img src="{{ $client->icon_url }}" alt="Current Icon" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                            </div>
                        @endif
                        
                        <!-- New Image Preview -->
                        <div id="icon-preview" class="mt-2" style="display: none;">
                            <label class="form-label">New Icon Preview:</label><br>
                            <img id="preview-image" src="" alt="Icon Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                        </div>
                    </div>
                        <div class="form-text">Enter a valid URL for the client icon/logo</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Icon</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($client->icon_url)
                        <img src="{{ $client->icon_url }}" alt="{{ $client->title }}" 
                             class="rounded border" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; margin: 0 auto;">
                            <i class="bi bi-image text-muted fs-1"></i>
                        </div>
                    @endif
                </div>
                <small class="text-muted">Current client icon</small>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Icon Preview</h5>
            </div>
            <div class="card-body text-center">
                <div id="icon-preview" class="mb-3">
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; margin: 0 auto;">
                        <i class="bi bi-image text-muted fs-1"></i>
                    </div>
                </div>
                <small class="text-muted">Preview will appear when you change the URL</small>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Client Info</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <strong>Projects:</strong><br>
                        <span class="badge bg-primary">{{ $client->projects->count() }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Created:</strong><br>
                        <small class="text-muted">{{ $client->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        @if($client->projects->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Associated Projects</h5>
            </div>
            <div class="card-body">
                @foreach($client->projects->take(5) as $project)
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-folder text-primary me-2"></i>
                        <small>{{ $project->title }}</small>
                    </div>
                @endforeach
                @if($client->projects->count() > 5)
                    <small class="text-muted">and {{ $client->projects->count() - 5 }} more...</small>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<script>
// Image Preview functionality
document.getElementById('icon').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('icon-preview');
    const previewImage = document.getElementById('preview-image');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Create Client')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Create New Client</h1>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Clients
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.clients.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Name *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Client Icon *</label>
                        <input type="file" class="form-control @error('icon') is-invalid @enderror" 
                               id="icon" name="icon" accept="image/*" required>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Upload client icon/logo (JPEG, PNG, GIF, WebP, SVG - Max: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB)</div>
                        
                        <!-- Image Preview -->
                        <div id="icon-preview" class="mt-2" style="display: none;">
                            <img id="preview-image" src="" alt="Icon Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Icon Preview</h5>
            </div>
            <div class="card-body text-center">
                <div id="current-preview" class="mb-3">
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; margin: 0 auto;">
                        <i class="bi bi-image text-muted fs-1"></i>
                    </div>
                </div>
                <small class="text-muted">Icon will appear here when you select a file</small>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="bi bi-check-circle text-success"></i> Use company's official name</li>
                    <li><i class="bi bi-check-circle text-success"></i> Write clear description</li>
                    <li><i class="bi bi-check-circle text-success"></i> Use high-quality logo</li>
                    <li><i class="bi bi-check-circle text-success"></i> Logo should be square format</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Image Preview functionality
document.getElementById('icon').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const currentPreview = document.getElementById('current-preview');
    const iconPreview = document.getElementById('icon-preview');
    const previewImage = document.getElementById('preview-image');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            currentPreview.innerHTML = `<img src="${e.target.result}" alt="Icon Preview" class="rounded border" style="width: 100px; height: 100px; object-fit: cover; margin: 0 auto;">`;
            
            // Also update the main preview if exists
            if (iconPreview && previewImage) {
                previewImage.src = e.target.result;
                iconPreview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    } else {
        currentPreview.innerHTML = `<div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; margin: 0 auto;">
                                        <i class="bi bi-image text-muted fs-1"></i>
                                    </div>`;
        if (iconPreview) {
            iconPreview.style.display = 'none';
        }
    }
});
</script>
@endsection

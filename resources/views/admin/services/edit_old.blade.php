@extends('layouts.admin')

@section('title', 'Edit Service - Havor Admin')
@section('page-title', 'Edit Service: ' . $service->name)

@section('page-actions')
    <a href="{{ route('admin.services.show', $service) }}" class="btn btn-info">
        <i class="bi bi-eye"></i> View Service
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
                    <i class="bi bi-pencil"></i> Edit Service Information
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $service->name) }}" 
                               placeholder="Enter service name" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" required>{{ old('description', $service->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="features" class="form-label">Features</label>
                        <textarea class="form-control @error('features') is-invalid @enderror" 
                                  id="features" name="features" rows="3" 
                                  placeholder="e.g., Feature 1, Feature 2, Feature 3">{{ old('features', $service->features) }}</textarea>
                        @error('features')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Separate features with commas</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $service->price) }}" step="0.01" required>
                                </div>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" name="duration" value="{{ old('duration', $service->duration) }}" 
                                       placeholder="e.g., 2-4 weeks">
                                @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="icon" class="form-label">Service Icon</label>
                        <input type="file" class="form-control @error('icon') is-invalid @enderror" 
                               id="icon" name="icon" accept="image/*">
                        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Upload a new icon to replace the current one (JPEG, PNG, GIF, WebP, SVG - Max: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB)</div>
                        
                        @if($service->icon_url)
                            <div class="mt-2">
                                <label class="form-label">Current Icon:</label><br>
                                <img src="{{ $service->icon_url }}" alt="Current Icon" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                            </div>
                        @endif
                        
                        <!-- New Image Preview -->
                        <div id="icon-preview" class="mt-2" style="display: none;">
                            <label class="form-label">New Icon Preview:</label><br>
                            <img id="preview-image" src="" alt="Icon Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">Feature this service</label>
                    </div>
                        <input type="url" class="form-control @error('icon_url') is-invalid @enderror" 
                               id="icon_url" name="icon_url" value="{{ old('icon_url', $service->icon_url) }}" 
                               placeholder="https://example.com/icon.png">
                        @error('icon_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional: URL to service icon/image</div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="order_index" class="form-label">Display Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('order_index') is-invalid @enderror" 
                               id="order_index" name="order_index" value="{{ old('order_index', $service->order_index) }}" 
                               min="0" required>
                        @error('order_index')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Lower numbers appear first</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.services.show', $service) }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-info-circle"></i> Current Service
                </h6>
            </div>
            <div class="card-body">
                @if($service->icon_url)
                    <div class="text-center mb-3">
                        <img src="{{ $service->icon_url }}" alt="Service Icon" class="img-fluid" style="max-width: 100px;">
                    </div>
                @endif
                
                <table class="table table-sm">
                    <tr>
                        <th>Created:</th>
                        <td>{{ $service->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Updated:</th>
                        <td>{{ $service->updated_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Projects:</th>
                        <td>{{ $service->projects->count() }} linked</td>
                    </tr>
                </table>
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
                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline">
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
@endsection --}}

@extends('layouts.admin')

@section('title', 'Edit Service - Havor Admin')
@section('page-title', 'Edit Service: ' . $service->name)

@section('page-actions')
    <a href="{{ route('admin.services.show', $service) }}" class="btn btn-info">
        <i class="bi bi-eye"></i> View Service
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
                    <i class="bi bi-pencil"></i> Edit Service Information
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.services.update', $service) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $service->name) }}" 
                               placeholder="Enter service name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  required>{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="features" class="form-label">Features <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('features') is-invalid @enderror" 
                                  id="features" name="features" rows="4" 
                                  required>{{ old('features', $service->features) }}</textarea>
                        @error('features')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price', $service->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="duration" class="form-label">Duration</label>
                        <input type="text" class="form-control @error('duration') is-invalid @enderror" 
                               id="duration" name="duration" value="{{ old('duration', $service->duration) }}">
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">
                            Mark as Featured Service
                        </label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

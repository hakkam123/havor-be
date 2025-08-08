@extends('layouts.admin')

@section('title', 'Create Industry')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Create New Industry</h1>
    <a href="{{ route('admin.industries.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Industries
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.industries.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Industry Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Brief description of the industry</div>
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Industry Icon</label>
                        <input type="file" class="form-control @error('icon') is-invalid @enderror" 
                               id="icon" name="icon" value="{{ old('icon') }}" placeholder="e.g., bi bi-building">
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.industries.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Industry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Industry Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="bi bi-check-circle text-success"></i> Use clear, descriptive names</li>
                    <li><i class="bi bi-check-circle text-success"></i> Add relevant descriptions</li>
                    <li><i class="bi bi-check-circle text-success"></i> Choose appropriate icons</li>
                    <li><i class="bi bi-check-circle text-success"></i> Keep names consistent</li>
                </ul>
                
                {{-- <hr>
                
                <h6>Icon Examples:</h6>
                <small class="text-muted">
                    <div><code>bi bi-building</code> - General business</div>
                    <div><code>bi bi-cpu</code> - Technology</div>
                    <div><code>bi bi-heart-pulse</code> - Healthcare</div>
                    <div><code>bi bi-tools</code> - Manufacturing</div>
                </small> --}}
            </div>
        </div>
    </div>
</div>
@endsection

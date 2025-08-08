@extends('layouts.admin')

@section('title', 'Create Article')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Create New Article</h1>
    <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Articles
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.articles.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description *</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="3" required>{{ old('short_description') }}</textarea>
                        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content *</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="author" class="form-label">Author *</label>
                        <input type="text" class="form-control @error('author') is-invalid @enderror" id="author" name="author" value="{{ old('author') }}" required>
                        @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="industry_id" class="form-label">Industry *</label>
                                <select class="form-select @error('industry_id') is-invalid @enderror" id="industry_id" name="industry_id" required>
                                    <option value="">Select industry</option>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry->id }}" {{ old('industry_id') == $industry->id ? 'selected' : '' }}>{{ $industry->name }}</option>
                                    @endforeach
                                </select>
                                @error('industry_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="services_id" class="form-label">Service *</label>
                                <select class="form-select @error('services_id') is-invalid @enderror" id="services_id" name="services_id" required>
                                    <option value="">Select service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('services_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                    @endforeach
                                </select>
                                @error('services_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL *</label>
                        <input type="file" class="form-control @error('image_url') is-invalid @enderror" id="image_url" name="image_url" value="{{ old('image_url') }}" required>
                        @error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

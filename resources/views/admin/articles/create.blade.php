@extends('layouts.admin')

@section('title', 'Create Article')
@section('page-title', 'Articles Management')

@section('page-actions')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Create New Article</h3>
    <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
      <i class="bi bi-arrow-left me-2"></i> Back
    </a>
  </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="bg-white rounded-lg card-shadow p-8">
    <div class="mb-8 flex items-center gap-3">
      <h3 class="font-semibold text-xl text-slate-800">Article Information</h3>
    </div>
    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-6">
          <div>
            <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('title') border-red-400 @enderror" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label for="short_description" class="block text-sm font-medium text-slate-700 mb-1">Short Description <span class="text-red-500">*</span></label>
            <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('short_description') border-red-400 @enderror" id="short_description" name="short_description" rows="3" required>{{ old('short_description') }}</textarea>
            @error('short_description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label for="author" class="block text-sm font-medium text-slate-700 mb-1">Author <span class="text-red-500">*</span></label>
            <input type="text" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('author') border-red-400 @enderror" id="author" name="author" value="{{ old('author') }}" required>
            @error('author')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="space-y-6">
          <div>
            <label for="content" class="block text-sm font-medium text-slate-700 mb-1">Content <span class="text-red-500">*</span></label>
            <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('content') border-red-400 @enderror" id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
            @error('content')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="industry_id" class="block text-sm font-medium text-slate-700 mb-1">Industry <span class="text-red-500">*</span></label>
              <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('industry_id') border-red-400 @enderror" id="industry_id" name="industry_id" required>
                <option value="">Select industry</option>
                @foreach($industries as $industry)
                  <option value="{{ $industry->id }}" {{ old('industry_id') == $industry->id ? 'selected' : '' }}>{{ $industry->name }}</option>
                @endforeach
              </select>
              @error('industry_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="services_id" class="block text-sm font-medium text-slate-700 mb-1">Service <span class="text-red-500">*</span></label>
              <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('services_id') border-red-400 @enderror" id="services_id" name="services_id" required>
                <option value="">Select service</option>
                @foreach($services as $service)
                  <option value="{{ $service->id }}" {{ old('services_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                @endforeach
              </select>
              @error('services_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
          </div>
          <div>
            <label for="image" class="block text-sm font-medium text-slate-700 mb-1">Article Image <span class="text-red-500">*</span></label>
            <input type="file" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('image') border-red-400 @enderror" id="image" name="image" accept="image/*" required>
            @error('image')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            <div class="text-xs text-slate-400 mt-1">Upload an article image (JPEG, PNG, GIF, WebP, SVG). Max size: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB</div>
            <div id="imagePreview" class="mt-2 hidden">
              <img id="previewImg" src="" alt="Preview" class="rounded bg-slate-100 p-2 shadow h-32 w-auto">
            </div>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end gap-2 pt-8">
        <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">Cancel</a>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">Create</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});
</script>
@endsection
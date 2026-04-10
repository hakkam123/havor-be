@extends('layouts.admin')

@section('title', 'Create Service - Havor Admin')
@section('page-title', 'Service Management')


@section('content')
<div class="max-w-5xl mx-auto">
  <div class="bg-white rounded-lg card-shadow p-8">

    <div class="mb-8 flex items-center gap-3">
      <h3 class="font-semibold text-xl text-slate-800">Service Information</h3>
    </div>

    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-6">
          <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Service Name <span class="text-red-500">*</span></label>
            <input type="text" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('name') border-red-400 @enderror"
                   id="name" name="name" value="{{ old('name') }}" 
                   placeholder="Enter service name" required>
            @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label for="short_description" class="block text-sm font-medium text-slate-700 mb-1">Short Description</label>
            <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('short_description') border-red-400 @enderror"
                      id="short_description" name="short_description" rows="2" 
                      placeholder="Brief description for preview">{{ old('short_description') }}</textarea>
            @error('short_description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            <div class="text-xs text-slate-400 mt-1">Maximum 500 characters</div>
          </div>
          <div>
            <label for="features" class="block text-sm font-medium text-slate-700 mb-1">Features</label>
            <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('features') border-red-400 @enderror"
                      id="features" name="features" rows="3" 
                      placeholder="e.g., Feature 1, Feature 2, Feature 3">{{ old('features') }}</textarea>
            @error('features')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            <div class="text-xs text-slate-400 mt-1">Separate features with commas</div>
          </div>
        </div>
        <div class="space-y-6">
          <div>
            <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
            <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('description') border-red-400 @enderror"
                      id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
            @error('description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label for="icon" class="block text-sm font-medium text-slate-700 mb-1">Service Icon</label>
            <input type="file" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('icon') border-red-400 @enderror"
                   id="icon" name="icon" accept="image/*">
            @error('icon')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            <div class="text-xs text-slate-400 mt-1">Upload an icon (JPEG, PNG, GIF, WebP, SVG - Max: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB)</div>
            <div id="icon-preview" class="mt-2 hidden">
              <img id="preview-icon" src="" alt="Icon Preview" class="rounded bg-slate-100 p-2 shadow h-20 w-auto">
            </div>
          </div>
          <div>
            <label for="hero_image" class="block text-sm font-medium text-slate-700 mb-1">Hero Image</label>
            <input type="file" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('hero_image') border-red-400 @enderror"
                   id="hero_image" name="hero_image" accept="image/*">
            @error('hero_image')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            <div class="text-xs text-slate-400 mt-1">Upload a hero image (JPEG, PNG, GIF, WebP, SVG - Max: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB)</div>
            <div id="hero-preview" class="mt-2 hidden">
              <img id="preview-hero" src="" alt="Hero Preview" class="rounded bg-slate-100 p-2 shadow h-28 w-auto">
            </div>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end gap-2 pt-8">
        <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">
          Cancel
        </a>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
          Create
        </button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('icon').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('icon-preview');
    const previewImage = document.getElementById('preview-icon');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});
document.getElementById('hero_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('hero-preview');
    const previewImage = document.getElementById('preview-hero');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});
</script>
@endsection
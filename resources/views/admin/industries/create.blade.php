@extends('layouts.admin')

@section('title', 'Create Industry')
@section('page-title', 'Industries Management')

@section('page-actions')
  <div class="flex items-center justify-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Create Industry</h3>
    <a href="{{ route('admin.industries.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
  <div class="md:col-span-2">
    <div class="bg-white rounded-lg card-shadow p-8">
      <form action="{{ route('admin.industries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
          <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Industry Name <span class="text-red-500">*</span></label>
          <input type="text" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 bg-slate-50 px-4 py-3 outline-none @error('name') border-red-400 @enderror"
                 id="name" name="name" value="{{ old('name') }}" required>
          @error('name')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div>
          <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
          <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 bg-slate-50 px-4 py-3 outline-none @error('description') border-red-400 @enderror"
                    id="description" name="description" rows="4">{{ old('description') }}</textarea>
          @error('description')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
          @enderror
          <div class="text-xs text-slate-400 mt-1">Brief description of the industry</div>
        </div>

        <div>
          <label for="icon" class="block text-sm font-semibold text-slate-700 mb-1">Industry Icon</label>
          <input type="file" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('icon') border-red-400 @enderror"
                 id="icon" name="icon" accept="image/*">
          @error('icon')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
          @enderror
          <div class="text-xs text-slate-400 mt-1">Upload an icon image (JPEG, PNG, GIF, WebP, SVG). Max size: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB</div>
          <div id="icon-preview" class="mt-2 hidden">
            <label class="block text-xs text-slate-500 mb-1">New Icon Preview:</label>
            <img id="preview-image" src="" alt="Icon Preview" class="rounded bg-slate-100 p-2 shadow h-24 w-auto mx-auto">
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-8">
          <a href="{{ route('admin.industries.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">Cancel</a>
          <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">Create</button>
        </div>
      </form>
    </div>
  </div>

  <div>
    <div class="bg-white rounded-lg card-shadow p-6">
      <h5 class="font-semibold text-slate-700 mb-4">Industry Guidelines</h5>
      <ul class="space-y-2">
        <li class="flex items-center gap-2 text-slate-700"><i class="bi bi-check-circle text-emerald-500"></i> Use clear, descriptive names</li>
        <li class="flex items-center gap-2 text-slate-700"><i class="bi bi-check-circle text-emerald-500"></i> Add relevant descriptions</li>
        <li class="flex items-center gap-2 text-slate-700"><i class="bi bi-check-circle text-emerald-500"></i> Choose appropriate icons</li>
        <li class="flex items-center gap-2 text-slate-700"><i class="bi bi-check-circle text-emerald-500"></i> Keep names consistent</li>
      </ul>
    </div>
  </div>
</div>

<script>
document.getElementById('icon').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('icon-preview');
    const previewImage = document.getElementById('preview-image');
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
@extends('layouts.admin')

@section('title', 'Edit Client')
@section('page-title', 'Clients Management')

@section('page-actions')
  <div class="flex items-center justify-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Edit Client</h3>
    <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition mt-3">
      <i class="bi bi-arrow-left"></i> Back to Clients
    </a>
  </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
  <div class="md:col-span-2 bg-white rounded-lg card-shadow p-8">
    <form action="{{ route('admin.clients.update', $client) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf
      @method('PUT')

      <div>
        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input type="text" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('title') border-red-400 @enderror"
               id="title" name="title" value="{{ old('title', $client->title) }}" required>
        @error('title')
          <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div>
        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
        <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('description') border-red-400 @enderror"
                  id="description" name="description" rows="4" required>{{ old('description', $client->description) }}</textarea>
        @error('description')
          <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div>
        <label for="icon" class="block text-sm font-medium text-slate-700 mb-1">Client Icon</label>
        <input type="file" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('icon') border-red-400 @enderror"
               id="icon" name="icon" accept="image/*">
        @error('icon')
          <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
        <div class="text-xs text-slate-400 mt-1">Upload a new icon to replace the current one (JPEG, PNG, GIF, WebP, SVG - Max: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB)</div>
        <div id="icon-preview" class="mt-2 hidden">
          <label class="block text-xs text-slate-500 mb-1">New Icon Preview:</label>
          <img id="preview-image" src="" alt="Icon Preview" class="rounded bg-slate-100 p-2 shadow h-24 w-auto mx-auto">
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 pt-8">
        <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">Cancel</a>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">Update</button>
      </div>
    </form>
  </div>

  <div>
    <div class="bg-white rounded-lg card-shadow p-6 mb-6">
      <h5 class="font-semibold text-slate-700 mb-4">Current Icon</h5>
      <div class="flex items-center justify-center mb-3">
        @if($client->icon_url)
          <img src="{{ asset('storage/' . $client->icon_url) }}" alt="{{ $client->title }}" 
               class="rounded bg-slate-100 h-48 w-72 object-cover mx-auto">
        @else
          <div class="bg-slate-100 border rounded flex items-center justify-center h-48 w-72 mx-auto">
            <i class="bi bi-image text-slate-300 text-4xl"></i>
          </div>
        @endif
      </div>
      <small class="text-slate-400 block text-center">Current client icon</small>
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
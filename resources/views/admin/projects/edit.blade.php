@extends('layouts.admin')

@section('title', 'Edit Project')
@section('page-title', 'Projects Management')

@section('page-actions')
  <div class="flex items-center justify-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Edit Project</h3>
    <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
  <div class="md:col-span-2">
    <div class="bg-white rounded-lg card-shadow p-8">
        
      <form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
          <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">Title <span class="text-red-500">*</span></label>
          <input type="text" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 bg-slate-50 px-4 py-3 outline-none @error('title') border-red-400 @enderror"
                 id="title" name="title" value="{{ old('title', $project->title) }}" required>
          @error('title')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div>
          <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
          <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 bg-slate-50 px-4 py-3 outline-none @error('description') border-red-400 @enderror"
                    id="description" name="description" rows="3" required>{{ old('description', $project->description) }}</textarea>
          @error('description')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div>
          <label for="content" class="block text-sm font-semibold text-slate-700 mb-1">Content <span class="text-red-500">*</span></label>
          <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 bg-slate-50 px-4 py-3 outline-none @error('content') border-red-400 @enderror"
                    id="content" name="content" rows="6" required>{{ old('content', $project->content) }}</textarea>
          @error('content')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label for="client_id" class="block text-sm font-semibold text-slate-700 mb-1">Client <span class="text-red-500">*</span></label>
            <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 outline-none @error('client_id') border-red-400 @enderror"
                    id="client_id" name="client_id" required>
              <option value="">Select a client</option>
              @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                  {{ $client->title }}
                </option>
              @endforeach
            </select>
            @error('client_id')
              <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
            <div class="text-xs text-slate-400 mt-1">
              Don't see your client? <a href="{{ route('admin.clients.create') }}" target="_blank" class="underline text-sky-600">Create a new client</a>
            </div>
          </div>
          <div>
            <label for="service_id" class="block text-sm font-semibold text-slate-700 mb-1">Service <span class="text-red-500">*</span></label>
            <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 outline-none @error('service_id') border-red-400 @enderror"
                    id="service_id" name="service_id" required>
              <option value="">Select a service</option>
              @foreach($services as $service)
                <option value="{{ $service->id }}" {{ old('service_id', $project->service_id ?? '') == $service->id ? 'selected' : '' }}>
                  {{ $service->name }}
                </option>
              @endforeach
            </select>
            @error('service_id')
              <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
            <div class="text-xs text-slate-400 mt-1">
              Don't see your service? <a href="{{ route('admin.services.create') }}" target="_blank" class="underline text-sky-600">Create a new service</a>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label for="project_date" class="block text-sm font-semibold text-slate-700 mb-1">Project Date <span class="text-red-500">*</span></label>
            <input type="date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 outline-none @error('project_date') border-red-400 @enderror"
                   id="project_date" name="project_date"
                   value="{{ old('project_date', $project->project_date ? \Carbon\Carbon::parse($project->project_date)->format('Y-m-d') : '') }}"
                   required>
            @error('project_date')
              <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
          </div>
          <div>
            <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 outline-none @error('status') border-red-400 @enderror"
                    id="status" name="status" required>
              <option value="">Select status</option>
              <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning</option>
              <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
              <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            @error('status')
              <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div>
          <label for="image" class="block text-sm font-semibold text-slate-700 mb-1">Project Image</label>
          <input type="file" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('image') border-red-400 @enderror"
                 id="image" name="image" accept="image/*">
          @error('image')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
          @enderror
          <div class="text-xs text-slate-400 mt-1">Upload a new project image to replace current one (JPEG, PNG, GIF, WebP, SVG). Max size: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB</div>
          <div id="imagePreview" class="mt-2 hidden">
            <img id="previewImg" src="" alt="Preview" class="rounded bg-slate-100 p-2 shadow h-32 w-auto mx-auto">
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-8">
          <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">Cancel</a>
          <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">Update</button>
        </div>
      </form>
    </div>
  </div>

  <div>
    <div class="bg-white rounded-lg card-shadow p-6">
      <h5 class="font-semibold text-slate-700 mb-4">Current Image</h5>
      <div class="flex items-center justify-center mb-3">
        @if($project->image_url)
          <img src="{{ asset('storage/' . $project->image_url) }}" alt="{{ $project->title }}" class="rounded bg-slate-100 h-32 w-72 object-cover mx-auto">
        @else
          <div class="bg-slate-100 rounded flex items-center justify-center h-32 w-72 mx-auto">
            <i class="bi bi-image text-slate-300 text-4xl"></i>
          </div>
        @endif
      </div>
      <small class="text-slate-400 block text-center">Current project image</small>
    </div>
  </div>
</div>

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
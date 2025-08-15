@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Products Management')

@section('page-actions')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Edit Product</h3>
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded shadow hover:bg-slate-200 transition mt-3">
      <i class="bi bi-arrow-left me-2"></i> Back to Products
    </a>
  </div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
  <div class="md:col-span-2">
    <div class="bg-white rounded-lg card-shadow p-8">
      <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-6">
            <div>
              <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Product Name <span class="text-red-500">*</span></label>
              <input type="text" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('name') border-red-400 @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
              @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
              <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('description') border-red-400 @enderror" id="description" name="description" rows="3" required>{{ old('description', $product->description) }}</textarea>
              @error('description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="features" class="block text-sm font-medium text-slate-700 mb-1">Features <span class="text-red-500">*</span></label>
              <textarea class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('features') border-red-400 @enderror" id="features" name="features" rows="4" required>{{ old('features', $product->features) }}</textarea>
              @error('features')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
              <div class="text-xs text-slate-400 mt-1">List the key features of this product</div>
            </div>
            <div>
              <label for="category" class="block text-sm font-medium text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
              <input type="text" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 px-4 py-3 outline-none @error('category') border-red-400 @enderror" id="category" name="category" value="{{ old('category', $product->category) }}" required>
              @error('category')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="space-y-6">
            <div>
              <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Price <span class="text-red-500">*</span></label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">$</span>
                <input type="number" step="0.01" min="0" class="pl-7 w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-100 bg-slate-50 py-3 outline-none @error('price') border-red-400 @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
              </div>
              @error('price')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
              <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('status') border-red-400 @enderror" id="status" name="status" required>
                <option value="">Select status</option>
                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
              @error('status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="hero_image" class="block text-sm font-medium text-slate-700 mb-1">Hero Image</label>
              <input type="file" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('hero_image') border-red-400 @enderror" id="hero_image" name="hero_image" accept="image/*">
              @error('hero_image')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
              <div class="text-xs text-slate-400 mt-1">Upload a new hero image to replace the current one (JPEG, PNG, GIF, WebP, SVG). Max size: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB</div>
              <div id="heroImagePreview" class="mt-2 hidden">
                <img id="previewHeroImg" src="" alt="Hero Preview" class="rounded bg-sky-50 p-2 shadow h-32 w-auto border border-sky-200">
              </div>
            </div>
            <div>
              <label for="content_image" class="block text-sm font-medium text-slate-700 mb-1">Content Image</label>
              <input type="file" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 @error('content_image') border-red-400 @enderror" id="content_image" name="content_image" accept="image/*">
              @error('content_image')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
              <div class="text-xs text-slate-400 mt-1">Upload a new content image to replace the current one (JPEG, PNG, GIF, WebP, SVG). Max size: {{ env('APP_UPLOAD_MAX_SIZE', 2048) }}KB</div>
              <div id="contentImagePreview" class="mt-2 hidden">
                <img id="previewContentImg" src="" alt="Content Preview" class="rounded bg-sky-50 p-2 shadow h-32 w-auto border border-sky-200">
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-2 pt-8">
          <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">Cancel</a>
          <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-amber-500 to-yellow-500 text-white rounded shadow hover:from-amber-600 hover:to-yellow-600 transition font-semibold">
            <i class="bi bi-check-circle me-1"></i> Update Product
          </button>
        </div>
      </form>
    </div>
  </div>
  <div>
    <div class="bg-slate-50 rounded-lg card-shadow p-6 mb-6">
      <h5 class="mb-4 font-semibold text-sky-700 flex items-center gap-2"><i class="bi bi-image"></i> Current Hero Image</h5>
      <div class="flex justify-center items-center">
        @if($product->hero_image_url)
          <img src="{{ asset('storage/' . $product->hero_image_url) }}" alt="{{ $product->name }}" class="rounded bg-sky-50 p-2 shadow h-32 w-auto border border-sky-200">
        @else
          <div class="flex flex-col items-center text-slate-400">
            <i class="bi bi-image text-5xl"></i>
            <p class="mt-2">No hero image available</p>
          </div>
        @endif
      </div>
    </div>
    @if($product->content_image_url)
    <div class="bg-slate-50 rounded-lg card-shadow p-6">
      <h5 class="mb-4 font-semibold text-sky-700 flex items-center gap-2"><i class="bi bi-image"></i> Current Content Image</h5>
      <div class="flex justify-center items-center">
        <img src="{{ asset('storage/' . $product->content_image_url) }}" alt="{{ $product->name }}" class="rounded bg-sky-50 p-2 shadow h-32 w-auto border border-sky-200">
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('hero_image').addEventListener('change', function(e) {
  const file = e.target.files[0];
  const preview = document.getElementById('heroImagePreview');
  const previewImg = document.getElementById('previewHeroImg');
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

document.getElementById('content_image').addEventListener('change', function(e) {
  const file = e.target.files[0];
  const preview = document.getElementById('contentImagePreview');
  const previewImg = document.getElementById('previewContentImg');
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
@extends('layouts.admin')

@section('title', 'Product Details')
@section('page-title', 'Products Management')

@section('page-actions')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Product Details</h3>
    <div class="flex gap-2">
      <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded shadow hover:bg-amber-600 transition mt-3">
        <i class="bi bi-pencil me-2"></i> Edit
      </a>
      <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded shadow hover:bg-slate-200 transition mt-3">
        <i class="bi bi-arrow-left me-2"></i> Back to Products
      </a>
    </div>
  </div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-5 gap-8">
  <div class="md:col-span-3">
    <div class="bg-white rounded-lg card-shadow p-8 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center mb-6">
        <div class="flex flex-col items-center">
          @if($product->hero_image_url)
            <img src="{{ asset('storage/' . $product->hero_image_url) }}" alt="{{ $product->name }}"
                 class="rounded bg-sky-50 p-2 shadow border border-sky-200 mb-2" style="width:120px; height:120px; object-fit:cover;">
          @else
            <div class="flex items-center justify-center rounded bg-slate-100 border border-slate-300 mb-2" style="width:120px; height:120px;">
              <i class="bi bi-image text-slate-400 text-5xl"></i>
            </div>
          @endif
        </div>
        <div class="md:col-span-3">
          <h2 class="font-bold text-xl text-slate-800 mb-1">{{ $product->name }}</h2>
          <p class="text-slate-500 mb-3">{{ $product->description }}</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
            <div>
              <div class="font-semibold text-slate-500 mb-1">Category:</div>
              <span class="inline-block px-2 py-0.5 rounded bg-sky-100 text-sky-700 text-xs font-semibold">{{ $product->category }}</span>
            </div>
            <div>
              <div class="font-semibold text-slate-500 mb-1">Price:</div>
              <span class="h4 text-emerald-600 font-bold">${{ number_format($product->price, 2) }}</span>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <div class="font-semibold text-slate-500 mb-1">Status:</div>
              @if($product->status === 'active')
                <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs font-semibold">Active</span>
              @else
                <span class="inline-block px-2 py-0.5 rounded bg-slate-200 text-slate-500 text-xs font-semibold">Inactive</span>
              @endif
            </div>
            <div>
              <div class="font-semibold text-slate-500 mb-1">Created:</div>
              <span class="text-slate-500">{{ $product->created_at->format('F d, Y') }}</span>
            </div>
          </div>
        </div>
      </div>
      <hr class="my-6">
      <div class="mb-8">
        <h5 class="font-semibold text-slate-700 mb-2">Product Features</h5>
        <div class="bg-slate-50 p-4 rounded">{!! nl2br(e($product->features)) !!}</div>
      </div>
      @if($product->content_image_url)
      <div class="mb-8">
        <h5 class="font-semibold text-slate-700 mb-2">Content Image</h5>
        <div class="flex justify-center">
          <img src="{{ asset('storage/' . $product->content_image_url) }}" alt="{{ $product->name }}"
               class="rounded bg-sky-50 p-2 shadow border border-sky-200" style="max-height:400px;">
        </div>
      </div>
      @endif
    </div>
  </div>
  <div class="md:col-span-2 flex flex-col gap-6">
    <div class="bg-white rounded-lg card-shadow p-6">
      <h5 class="mb-4 font-semibold text-sky-700">Product Information</h5>
      <div class="mb-4 flex items-center gap-3">
        <span class="font-semibold text-slate-500">Hero Image:</span>
        @if($product->hero_image_url)
          <img src="{{ asset('storage/' . $product->hero_image_url) }}" alt="Hero" class="rounded border bg-sky-50" style="width:32px; height:32px; object-fit:cover;">
          <span class="text-slate-400 text-xs">{{ $product->hero_image_url }}</span>
        @else
          <span class="text-slate-400 text-xs">No hero image</span>
        @endif
      </div>
      <div class="mb-4 flex items-center gap-3">
        <span class="font-semibold text-slate-500">Content Image:</span>
        @if($product->content_image_url)
          <img src="{{ asset('storage/' . $product->content_image_url) }}" alt="Content" class="rounded border bg-sky-50" style="width:32px; height:32px; object-fit:cover;">
          <span class="text-slate-400 text-xs">{{ $product->content_image_url }}</span>
        @else
          <span class="text-slate-400 text-xs">No content image</span>
        @endif
      </div>
      <div class="mb-4">
        <span class="font-semibold text-slate-500">Created:</span><br>
        <span class="text-slate-400">{{ $product->created_at->format('F d, Y \a\t H:i') }}</span>
      </div>
      <div class="mb-2">
        <span class="font-semibold text-slate-500">Last Updated:</span><br>
        <span class="text-slate-400">{{ $product->updated_at->format('F d, Y \a\t H:i') }}</span>
      </div>
    </div>
    <div class="bg-white rounded-lg card-shadow p-6">
      <h5 class="mb-4 font-semibold text-sky-700">Actions</h5>
      <div class="flex flex-col gap-3">
        <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white rounded shadow hover:bg-amber-600 transition">
          <i class="bi bi-pencil"></i> Edit Product
        </a>
        <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700 transition" 
                onclick="showDeleteModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
          <i class="bi bi-trash"></i> Delete Product
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Delete Confirmation -->
<div
  id="deleteModalProduct"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden"
  style="backdrop-filter: blur(2px);"
>
  <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-auto p-6 relative">
    <button type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700" onclick="hideDeleteModal()">
      <i class="bi bi-x-lg text-xl"></i>
    </button>
    <div class="flex items-center gap-2 mb-4">
      <i class="bi bi-exclamation-triangle text-red-500 text-2xl"></i>
      <span class="font-semibold text-lg text-red-600">Confirm Delete</span>
    </div>
    <div class="mb-2 text-slate-700">
      Are you sure you want to delete product <span id="deleteProductName" class="font-semibold text-red-500"></span>?
      <div class="mt-1 text-xs text-slate-500">This action cannot be undone.</div>
    </div>
    <div class="flex gap-2 pt-4">
      <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition w-full justify-center" onclick="hideDeleteModal()">Cancel</button>
      <form id="deleteProductForm" method="POST" class="w-full flex justify-center">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700 transition w-full justify-center font-semibold">
          <i class="bi bi-trash me-1"></i> Yes, Delete
        </button>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function showDeleteModal(productId, productName) {
  document.getElementById('deleteModalProduct').classList.remove('hidden');
  document.getElementById('deleteProductName').innerText = '"' + productName + '"';
  var form = document.getElementById('deleteProductForm');
  form.action = "{{ route('admin.products.destroy', '__id__') }}".replace('__id__', productId);
}
function hideDeleteModal() {
  document.getElementById('deleteModalProduct').classList.add('hidden');
}
</script>
@endsection
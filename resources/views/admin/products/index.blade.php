@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products Management')

@section('page-actions')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Products</h3>
    <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
      <i class="bi bi-plus-circle me-2"></i> Add New Product
    </a>
  </div>
@endsection

@section('content')
@if(session('success'))
  <div class="mb-4 rounded-md bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg card-shadow p-8">
  <div class="flex items-center justify-between mb-4">
    <h3 class="font-semibold text-slate-700 text-lg">All Products</h3>
    <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-[#4178be] text-white rounded shadow hover:bg-[#3569a8] transition">
      Add Product
    </a>
  </div>
  @if($products->count() > 0)
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b">
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:48px;">ID</th>
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:70px;">Hero Image</th>
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:220px;">Name & Description</th>
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:100px;">Category</th>
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:80px;">Price</th>
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:90px;">Status</th>
            <th class="py-2 text-center text-xs font-semibold text-slate-500" style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($products as $product)
            <tr class="hover:bg-slate-50 transition">
              <td class="py-3 font-semibold text-slate-700">{{ $product->id }}</td>
              <td class="py-3">
                @if($product->hero_image_url)
                  <img src="{{ asset('storage/' . $product->hero_image_url) }}" alt="{{ $product->name }}" class="rounded bg-slate-100 object-cover h-12 w-12">
                @else
                  <div class="bg-slate-200 rounded flex items-center justify-center h-12 w-12">
                    <i class="bi bi-image text-slate-400 text-xl"></i>
                  </div>
                @endif
              </td>
              <td class="py-3" style="min-width:160px;">
                <div class="font-medium text-slate-700">{{ $product->name }}</div>
                <div class="text-slate-400 text-xs">{{ Str::limit($product->description, 50) }}</div>
              </td>
              <td class="py-3">
                <span class="inline-block px-2 py-0.5 rounded bg-sky-100 text-sky-700 text-xs font-semibold">{{ $product->category }}</span>
              </td>
              <td class="py-3 text-slate-700">${{ number_format($product->price, 2) }}</td>
              <td class="py-3">
                @if($product->status === 'active')
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs font-semibold">Active</span>
                @else
                  <span class="inline-block px-2 py-0.5 rounded bg-slate-200 text-slate-500 text-xs font-semibold">Inactive</span>
                @endif
              </td>
              <td class="py-3 text-center">
                <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-amber-500 bg-white hover:bg-amber-50 me-1" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <button type="button"
                  class="inline-flex items-center justify-center px-2 py-1 border rounded text-red-600 bg-white hover:bg-red-50"
                  title="Delete"
                  onclick="showDeleteModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="flex justify-center mt-6">
      {{ $products->links() }}
    </div>
  @else
    <div class="flex flex-col items-center py-10">
      <i class="bi bi-box-seam text-slate-300 mb-4" style="font-size: 4rem;"></i>
      <h4 class="text-slate-400 font-semibold mb-1">No Products Found</h4>
      <p class="text-slate-400">Get started by creating your first product.</p>
    </div>
  @endif
</div>

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
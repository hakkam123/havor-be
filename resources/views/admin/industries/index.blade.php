@extends('layouts.admin')

@section('title', 'Industries')
@section('page-title', 'Industries Management')



@section('content')

@if(session('success'))
  <div class="mb-4 rounded-md bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</div>
@endif

@if(session('error'))
  <div class="mb-4 rounded-md bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg card-shadow p-8">
  <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-slate-700 text-lg"> All Industries</h3>
    <a href="{{ route('admin.industries.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">Add New Industry
    </a>
</div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b text-slate-500">
          <th class="py-2 text-left text-xs font-semibold" style="width:60px;">No</th>
          <th class="py-2 text-left text-xs font-semibold" style="min-width:70px;">Name</th>
          <th class="py-2 text-left text-xs font-semibold" style="min-width:190px;">Description</th>
          <th class="py-2 text-left text-xs font-semibold" style="width:90px;">Icon</th>
          <th class="py-2 text-left text-xs font-semibold" style="width:120px;">Articles Count</th>
          <th class="py-2 text-center text-xs font-semibold" style="width:120px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($industries as $industry)
          <tr class="hover:bg-slate-50 transition">
            <td class="py-3">{{ $industry->id }}</td>
            <td class="py-3 font-medium text-slate-700">{{ $industry->name }}</td>
            <td class="py-3 text-slate-500">{{ Str::limit($industry->description, 50) }}</td>
            <td class="py-3">
              @if($industry->icon)
                <i class="{{ $industry->icon }} text-lg"></i>
              @else
                <span class="text-slate-400 text-xs">No icon</span>
              @endif
            </td>
            <td class="py-3">
              <span class="inline-block px-2 py-0.5 rounded ml-6 bg-sky-100 text-sky-700 text-xs font-semibold">{{ $industry->articles_count ?? 0 }}</span>
            </td>
            <td class="py-3 text-center">
              <a href="{{ route('admin.industries.edit', $industry) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-amber-500 bg-white hover:bg-amber-50 me-1" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.industries.destroy', $industry) }}" method="POST" class="inline" 
                    onsubmit="return confirm('Are you sure you want to delete this industry? This will also delete all related articles.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                  class="inline-flex items-center justify-center px-2 py-1 border rounded text-red-600 bg-white hover:bg-red-50"
                  title="{{ $industry->articles_count > 0 ? 'Cannot delete industry with articles' : 'Delete' }}"
                  {{ $industry->articles_count > 0 ? 'disabled' : '' }}>
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-8 text-slate-400">
              <i class="bi bi-building-x text-3xl mb-2"></i>
              <div>No industries found</div>
              <a href="{{ route('admin.industries.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
                <i class="bi bi-plus-circle me-2"></i> Add First Industry
              </a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($industries->hasPages())
    <div class="mt-4 flex justify-end">
      {{ $industries->links() }}
    </div>
  @endif
</div>
@endsection
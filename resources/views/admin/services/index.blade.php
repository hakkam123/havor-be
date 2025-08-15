@extends('layouts.admin')

@section('title', 'Services - Havor Admin')
@section('page-title', 'Services Management')

@section('content')
<div class="space-y-6">
  <div class="bg-white rounded-lg card-shadow p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700 text-lg"> All Services</h3>
      <a href="{{ route('admin.services.create') }}" class="inline-flex items-center px-4 py-2 bg-[#4178be] text-white rounded shadow hover:bg-[#3569a8] transition">
        Add Service
      </a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b">
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:80px;">Icon</th>
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:140px;">Name</th>
            <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:300px;">Description</th>
            <th class="py-2 text-left text-xs font-semibold text-slate-500">Features</th>
            {{-- <th class="py-2 text-left text-xs font-semibold text-slate-500">Hero Image</th> --}}
            <th class="py-2 text-center text-xs font-semibold text-slate-500">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($services as $service)
            <tr class=" hover:bg-slate-50 transition">
              <td class="py-3">
                @if($service->icon_url)
                  <img src="{{ asset('storage/' . $service->icon_url) }}" alt="Icon" class="h-8 w-8 rounded bg-slate-100 object-contain">
                @else
                  <span class="text-slate-400 text-xs">No icon</span>
                @endif
              </td>
              <td class="py-3 font-medium text-slate-700">{{ $service->name }}</td>
              <td class="py-3 text-slate-500">{{ Str::limit($service->short_description, 80) ?: '-' }}</td>
              <td class="py-3 text-slate-500">{{ Str::limit($service->features, 60) ?: '-' }}</td>
              {{-- <td class="py-3">
                @if($service->hero_image)
                  <img src="{{ asset('storage/' . $service->hero_image) }}" alt="Hero" class="h-10 w-16 rounded bg-slate-100 object-cover">
                @else
                  <span class="text-slate-400 text-xs">No image</span>
                @endif
              </td> --}}
                <td class="py-3 text-center">
                    <a href="{{ route('admin.services.show', $service) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-green-600 bg-white hover:bg-green-50 me-1">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.services.edit', $service) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-sky-600 bg-white hover:bg-sky-50 me-1">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this service?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-2 py-1 border rounded text-red-600 bg-white hover:bg-red-50">
                        <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-8 text-slate-400">
                <i class="bi bi-tools text-3xl mb-2"></i>
                <div>No Services Found</div>
                <a href="{{ route('admin.services.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
                  <i class="bi bi-plus-circle me-2"></i> Add First Service
                </a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($services->hasPages())
      <div class="mt-4 flex justify-end">
        {{ $services->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Clients')
@section('page-title', 'Clients Management')

@section('page-actions')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Clients</h3>
    <a href="{{ route('admin.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
      <i class="bi bi-plus-circle me-2"></i> Add New Client
    </a>
  </div>
@endsection

@section('content')
@if(session('success'))
  <div class="mb-4 rounded-md bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</div>
@endif

@if(session('error'))
  <div class="mb-4 rounded-md bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg card-shadow p-8">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-slate-700 text-lg"> All Clients</h3>
      <a href="{{ route('admin.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-[#4178be] text-white rounded shadow hover:bg-[#3569a8] transition">
        Add Client
      </a>
    </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b">
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:40px;">No.</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:80px;">Icon</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:200px;">Name</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="max-width:320px; width:40%;">Description</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:100px;">Projects Count</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:110px;">Created At</th>
          <th class="py-2 text-center text-xs font-semibold text-slate-500" style="width:120px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($clients as $client)
          <tr class=" hover:bg-slate-50 transition">
            <td class="py-3">{{ $client->id }}</td>
            <td class="py-3">
              @if($client->icon_url)
                <img src="{{ asset('storage/' . $client->icon_url) }}" alt="{{ $client->title }}" class="rounded bg-slate-100 object-cover h-12 w-14 p-1">
              @else
                <div class="bg-slate-200 rounded flex items-center justify-center h-10 w-10">
                  <i class="bi bi-building text-slate-400 text-xl"></i>
                </div>
              @endif
            </td>
            <td class="py-3 font-medium text-slate-700" style="min-width:120px;">{{ $client->title }}</td>
            <td class="py-3 text-slate-500 max-w-[320px] truncate" style="max-width:320px;">{{ Str::limit($client->description, 80) }}</td>
            <td class="py-3">
              <span class="inline-block px-2 py-0.5 rounded bg-sky-100 text-sky-700 text-xs font-semibold">{{ $client->projects_count }} projects</span>
            </td>
            <td class="py-3 text-slate-500">{{ $client->created_at->format('M d, Y') }}</td>
            <td class="py-3 text-center">
              <a href="{{ route('admin.clients.show', $client) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-sky-600 bg-white hover:bg-sky-50 me-1" title="View">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('admin.clients.edit', $client) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-amber-500 bg-white hover:bg-amber-50 me-1" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this client?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center px-2 py-1 border rounded text-red-600 bg-white hover:bg-red-50" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-8 text-slate-400">
              <i class="bi bi-building-x text-3xl mb-2"></i>
              <div>No clients found</div>
              <a href="{{ route('admin.clients.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
                <i class="bi bi-plus-circle me-2"></i> Add First Client
              </a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($clients->hasPages())
    <div class="mt-4 flex justify-end">
      {{ $clients->links() }}
    </div>
  @endif
</div>
@endsection
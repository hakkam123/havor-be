@extends('layouts.admin')

@section('title', $service->title . ' - Havor Admin')
@section('page-title', 'Service Management')

@section('page-actions')
    <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 mt-3 transition">
        <i class="bi bi-arrow-left"></i> Back to Services
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-lg card-shadow p-8">
            <div class="flex items-center gap-3 mb-6">
                <h3 class="font-semibold text-xl text-slate-800">Service Details</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2">
                    <h2 class="font-bold text-2xl text-slate-700 mb-2">{{ $service->title }}</h2>
                    <p class="text-slate-500 mb-4">{{ $service->description }}</p>
                    <div class="space-y-3">
                        <div>
                            <span class="text-xs text-slate-500 font-semibold">Created</span>
                            <span class="ml-2 text-xs text-slate-700">{{ $service->created_at->format('F d, Y \a\t g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 font-semibold">Last Updated</span>
                            <span class="ml-2 text-xs text-slate-700">{{ $service->updated_at->format('F d, Y \a\t g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 font-semibold">Icon</span>
                            <span class="ml-2">
                                @if($service->icon_url)
                                    <img src="{{ asset('storage/' . $service->icon_url) }}" alt="Icon" class="inline-block rounded bg-slate-100 border p-1 h-8 w-8 object-cover align-middle mr-2">
                                    <span class="text-slate-400 text-xs">{{ $service->icon_url }}</span>
                                @else
                                    <span class="text-slate-400 text-xs">No icon set</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center text-center">
                    @if($service->icon_url)
                        <img src="{{ asset('storage/' . $service->icon_url) }}" alt="Service Icon" class="rounded bg-slate-100 border p-2 shadow h-32 w-auto mb-2">
                    @else
                        <i class="bi bi-image text-slate-300" style="font-size: 5rem;"></i>
                        <div class="text-slate-400 text-xs mt-2">No icon available</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg card-shadow p-8">
            <div class="flex items-center gap-3 mb-6">
                <h3 class="font-semibold text-xl text-slate-800">Related Projects ({{ $service->projects->count() }})</h3>
            </div>
            @if($service->projects->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-slate-500">
                                <th class="py-2 text-left">Project Title</th>
                                <th class="py-2 text-left">Client</th>
                                <th class="py-2 text-left">Created</th>
                                <th class="py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($service->projects as $project)
                                <tr class="border-b hover:bg-slate-50 transition">
                                    <td class="py-3">{{ $project->title }}</td>
                                    <td class="py-3">{{ $project->client_name }}</td>
                                    <td class="py-3">{{ $project->created_at->format('M d, Y') }}</td>
                                    <td class="py-3 text-center">
                                        <a href="{{ route('admin.projects.show', $project) }}" class="inline-flex items-center px-2 py-1 border rounded text-sky-600 bg-white hover:bg-sky-50">
                                             View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <h5 class="text-slate-400 mt-2 mb-1">No Related Projects</h5>
                    <p class="text-slate-400 mb-4">This service is not linked to any projects yet.</p>
                    <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
                         Create
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div>
        <div class="bg-white rounded-lg card-shadow p-8">
            <div class="flex items-center gap-3 mb-6">
                <h3 class="font-semibold text-xl text-slate-800">Actions</h3>
            </div>
            <div class="flex flex-col gap-3">
                <a href="{{ route('admin.services.edit', $service) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-400 text-white rounded shadow hover:bg-yellow-500 transition">
                    Edit Service
                </a>
                <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition">
                    All Services
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
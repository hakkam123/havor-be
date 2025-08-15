@extends('layouts.admin')

@section('title', $article->title)
@section('page-title', 'Articles Management')

@section('page-actions')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">{{ $article->title }}</h3>
    <div class="flex gap-2">
      <a href="{{ route('admin.articles.edit', $article) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded shadow hover:bg-amber-600 transition">
        <i class="bi bi-pencil me-2"></i> Edit
      </a>
      <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded shadow hover:bg-slate-200 transition">
        <i class="bi bi-arrow-left me-2"></i> Back
      </a>
    </div>
  </div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-5 gap-8">
  <div class="md:col-span-3">
    <div class="bg-white rounded-lg card-shadow p-8 mb-6">
      <div class="flex items-center gap-2 mb-3">
        <div class="text-slate-400"><i class="bi bi-person"></i></div>
        <h5 class="mb-0 text-slate-600 font-semibold">By <span class="text-slate-800">{{ $article->author }}</span></h5>
      </div>

      @if($article->image_url)
        <img src="{{ asset('storage/' . $article->image_url) }}" class="rounded-lg shadow mb-6 w-full object-cover max-h-64" alt="{{ $article->title }}">
      @endif

      <h6 class="text-slate-500 font-semibold mb-1">Short Description</h6>
      <p class="text-slate-700 mb-6">{{ $article->short_description }}</p>

      <h6 class="text-slate-500 font-semibold mb-1">Full Content</h6>
      <div class="prose max-w-none text-slate-800">{!! nl2br(e($article->content)) !!}</div>
    </div>
  </div>
  <div class="md:col-span-2">
    <div class="bg-white rounded-lg card-shadow p-6">
      <h5 class="mb-4 font-semibold text-slate-700">Details</h5>
      <div class="mb-4">
        <div class="font-semibold text-slate-500 mb-1">Industry:</div>
        @if($article->industry)
          <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-semibold">{{ $article->industry->name }}</span>
        @else
          <span class="text-slate-400 text-xs">N/A</span>
        @endif
      </div>
      <div class="mb-4">
        <div class="font-semibold text-slate-500 mb-1">Service:</div>
        @if($article->service)
          <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-semibold">{{ $article->service->name }}</span>
        @else
          <span class="text-slate-400 text-xs">N/A</span>
        @endif
      </div>
      <div class="mb-4">
        <div class="font-semibold text-slate-500 mb-1">Published:</div>
        <span class="text-slate-500">{{ $article->created_at->format('F d, Y') }}</span>
      </div>
      <div class="mb-2">
        <div class="font-semibold text-slate-500 mb-1">Last Updated:</div>
        <span class="text-slate-500">{{ $article->updated_at->format('F d, Y \a\t H:i') }}</span>
      </div>
    </div>
  </div>
</div>
@endsection
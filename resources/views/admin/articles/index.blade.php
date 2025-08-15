@extends('layouts.admin')

@section('title', 'Articles')
@section('page-title', 'Articles Management')

@section('page-actions')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 fw-bold text-dark">Articles</h3>
    <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
      <i class="bi bi-plus-circle me-2"></i> Add New Article
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
    <h3 class="font-semibold text-slate-700 text-lg">All Articles</h3>
    <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center px-4 py-2 bg-[#4178be] text-white rounded shadow hover:bg-[#3569a8] transition">
      Add Article
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b">
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:40px;">No.</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:160px;">Title</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:100px;">Author</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:120px;">Industry</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="width:120px;">Service</th>
          <th class="py-2 text-left text-xs font-semibold text-slate-500" style="max-width:280px; width:40%;">Short Description</th>
          <th class="py-2 text-center text-xs font-semibold text-slate-500" style="width:140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
          <tr class="hover:bg-slate-50 transition">
            <td class="py-3">{{ $loop->iteration }}</td>
            <td class="py-3 font-medium text-slate-700" style="min-width:100px;">{{ $article->title }}</td>
            <td class="py-3 text-slate-500 ml-4">{{ $article->author }}</td>
            <td class="py-3">
              @if($article->industry)
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-xs">{{ $article->industry->name }}</span>
              @else
                <span class="text-slate-400 text-xs">N/A</span>
              @endif
            </td>
            <td class="py-3">
              @if($article->service)
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-xs">{{ $article->service->name }}</span>
              @else
                <span class="text-slate-400 text-xs">N/A</span>
              @endif
            </td>
            <td class="py-3 text-slate-500 max-w-[320px] truncate" style="max-width:280px;">{{ Str::limit($article->short_description, 80) }}</td>
            <td class="py-3 text-center">
              <a href="{{ route('admin.articles.show', $article) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-sky-600 bg-white hover:bg-sky-50 me-1" title="View">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('admin.articles.edit', $article) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-amber-500 bg-white hover:bg-amber-50 me-1" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <button type="button"
                class="inline-flex items-center justify-center px-2 py-1 border rounded text-red-600 bg-white hover:bg-red-50"
                title="Delete"
                onclick="showDeleteModal('{{ $article->id }}', '{{ addslashes($article->title) }}')">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-8 text-slate-400">
              <i class="bi bi-folder-x text-3xl mb-2"></i>
              <div>No articles found</div>
              <a href="{{ route('admin.articles.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition">
                <i class="bi bi-plus-circle me-2"></i> Add First Article
              </a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($articles->hasPages())
    <div class="mt-4 flex justify-end">
      {{ $articles->links() }}
    </div>
  @endif
</div>

<div
  id="deleteModalArticle"
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
      Are you sure you want to delete the article <span id="deleteArticleTitle" class="font-semibold text-red-500"></span>?
      <div class="mt-1 text-xs text-slate-500">This action cannot be undone.</div>
    </div>
    <div class="flex gap-2 pt-4">
      <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition w-full justify-center" onclick="hideDeleteModal()">Cancel</button>
      <form id="deleteArticleForm" method="POST" class="w-full flex justify-center">
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
function showDeleteModal(articleId, articleTitle) {
  document.getElementById('deleteModalArticle').classList.remove('hidden');
  document.getElementById('deleteArticleTitle').innerText = '"' + articleTitle + '"';
  var form = document.getElementById('deleteArticleForm');
  form.action = "{{ route('admin.articles.destroy', '__id__') }}".replace('__id__', articleId);
}
function hideDeleteModal() {
  document.getElementById('deleteModalArticle').classList.add('hidden');
}
</script>
@endsection
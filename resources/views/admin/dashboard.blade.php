@extends('layouts.admin')

@section('title', 'Dashboard - Havor Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg p-4 card-shadow flex items-center justify-between">
      <div>
        <div class="text-xs font-semibold text-slate-800 uppercase">Services</div>
        <div class="text-2xl font-bold text-slate-800">{{ $stats['services'] }}</div>
      </div>
      <div class="text-slate-300">
        <i class="bi bi-tools text-3xl"></i>
      </div>
    </div>

    <div class="bg-white rounded-lg p-4 card-shadow flex items-center justify-between">
      <div>
        <div class="text-xs font-semibold text-slate-800 uppercase">Projects</div>
        <div class="text-2xl font-bold text-slate-800">{{ $stats['projects'] }}</div>
      </div>
      <div class="text-slate-300"><i class="bi bi-briefcase text-3xl"></i></div>
    </div>

    <div class="bg-white rounded-lg p-4 card-shadow flex items-center justify-between">
      <div>
        <div class="text-xs font-semibold text-slate-800 uppercase">Articles</div>
        <div class="text-2xl font-bold text-slate-800">{{ $stats['articles'] }}</div>
      </div>
      <div class="text-slate-300"><i class="bi bi-newspaper text-3xl"></i></div>
    </div>

    <div class="bg-white rounded-lg p-4 card-shadow flex items-center justify-between">
      <div>
        <div class="text-xs font-semibold text-slate-800 uppercase">Leads</div>
        <div class="text-2xl font-bold text-slate-800">{{ $stats['leads'] }}</div>
      </div>
      <div class="text-slate-300"><i class="bi bi-envelope text-3xl"></i></div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg card-shadow">
      <div class="px-4 py-3 border-b">
        <h3 class="font-semibold text-slate-700"> Recent Leads</h3>
      </div>
      <div class="p-4">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left text-slate-500">
              <tr>
                <th class="py-2">Name</th>
                <th class="py-2">Email</th>
                <th class="py-2">Date</th>
                <th class="py-2">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recent_leads as $lead)
                <tr>
                  <td class="py-3">{{ $lead->name }}</td>
                  <td class="py-3">{{ Str::limit($lead->email, 30) }}</td>
                  <td class="py-3">{{ $lead->created_at->format('M d') }}</td>
                  <td class="py-3">
                    <a href="{{ route('admin.leads.show', $lead) }}" class="inline-flex items-center px-2 py-1 border rounded text-sm text-sky-600 hover:bg-sky-50">
                      <i class="bi bi-eye"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center py-6 text-slate-400">No leads found</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($recent_leads->count() > 0)
          <div class="mt-3 text-center">
            <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded">View All Leads</a>
          </div>
        @endif
      </div>
    </div>

    <div class="bg-white rounded-lg card-shadow">
      <div class="px-4 py-3 border-b">
        <h3 class="font-semibold text-slate-700"> Recent Articles</h3>
      </div>
      <div class="p-4">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left text-slate-500">
              <tr>
                <th class="py-2">Title</th>
                <th class="py-2">Industry</th>
                <th class="py-2">Date</th>
                <th class="py-2">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recent_articles as $article)
                <tr>
                  <td class="py-3">{{ Str::limit($article->title, 30) }}</td>
                  <td class="py-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-xs">{{ $article->industry->name ?? 'N/A' }}</span></td>
                  <td class="py-3">{{ $article->created_at->format('M d') }}</td>
                  <td class="py-3">
                    <a href="{{ route('admin.articles.show', $article) }}" class="inline-flex items-center px-2 py-1 border rounded text-sm text-sky-600 hover:bg-sky-50">
                      <i class="bi bi-eye"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center py-6 text-slate-400">No articles found</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($recent_articles->count() > 0)
          <div class="mt-3 text-center">
            <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded">View All Articles</a>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="bg-white rounded-lg card-shadow p-4">
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
      <div>
        <div class="text-2xl font-bold">{{ $stats['products'] }}</div>
        <div class="text-xs text-slate-500">Products</div>
      </div>
      <div>
        <div class="text-2xl font-bold">{{ $stats['industries'] }}</div>
        <div class="text-xs text-slate-500">Industries</div>
      </div>
      <div>
        <div class="text-2xl font-bold">{{ $stats['homepage_features'] }}</div>
        <div class="text-xs text-slate-500">Features</div>
      </div>
      <div>
        <div class="text-2xl font-bold">{{ $recent_leads->count() }}</div>
        <div class="text-xs text-slate-500">Recent Leads</div>
      </div>
      <div>
        <div class="text-2xl font-bold">{{ $recent_articles->count() }}</div>
        <div class="text-xs text-slate-500">Recent Articles</div>
      </div>
      <div>
        <div class="text-2xl font-bold">—</div>
        <div class="text-xs text-slate-500">Placeholder</div>
      </div>
    </div>
  </div>

</div>
@endsection

@extends('layouts.admin')

@section('title', 'Dashboard - Havor Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Services</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['services'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Projects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['projects'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-briefcase fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Articles</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['articles'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-newspaper fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Leads</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['leads'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-envelope fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Leads -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-envelope-open"></i> Recent Leads
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_leads as $lead)
                                <tr>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ Str::limit($lead->email, 20) }}</td>
                                    <td>{{ $lead->created_at->format('M d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No leads found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recent_leads->count() > 0)
                    <div class="text-center">
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-right"></i> View All Leads
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Articles -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-newspaper"></i> Recent Articles
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Industry</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_articles as $article)
                                <tr>
                                    <td>{{ Str::limit($article->title, 25) }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $article->industry->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $article->created_at->format('M d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No articles found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recent_articles->count() > 0)
                    <div class="text-center">
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-right"></i> View All Articles
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-bar-chart"></i> Quick Overview
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <div class="border-right">
                            <h5>{{ $stats['products'] }}</h5>
                            <small class="text-muted">Products</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-right">
                            <h5>{{ $stats['industries'] }}</h5>
                            <small class="text-muted">Industries</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-right">
                            <h5>{{ $stats['homepage_features'] }}</h5>
                            <small class="text-muted">Features</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-right">
                            <h5>{{ $recent_leads->count() }}</h5>
                            <small class="text-muted">Recent Leads</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5>{{ $recent_articles->count() }}</h5>
                        <small class="text-muted">Recent Articles</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

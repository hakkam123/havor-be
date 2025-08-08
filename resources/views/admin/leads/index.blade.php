@extends('layouts.admin')

@section('title', 'Leads - Havor Admin')
@section('page-title', 'Leads Management')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-person-lines-fill"></i> All Leads
        </h6>
        <form class="d-flex" method="GET" action="{{ route('admin.leads.index') }}">
            <input type="text" name="search" class="form-control me-2" placeholder="Search..." value="{{ request('search') }}">
            <select name="status" class="form-select me-2">
                <option value="">All Status</option>
                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="card-body">
        @if($leads->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Submitted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $lead)
                            <tr>
                                <td>{{ $lead->name }}</td>
                                <td>{{ $lead->email }}</td>
                                <td>{{ $lead->company }}</td>
                                <td>
                                    <span class="badge bg-secondary text-capitalize">{{ $lead->status }}</span>
                                </td>
                                <td>{{ Str::limit($lead->notes, 50) }}</td>
                                <td>{{ $lead->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this lead?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $leads->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-person-lines-fill text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Leads Found</h4>
                <p class="text-muted">Try adjusting your filters or come back later.</p>
            </div>
        @endif
    </div>
</div>
@endsection

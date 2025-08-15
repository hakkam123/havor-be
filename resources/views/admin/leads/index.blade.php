@extends('layouts.admin')

@section('title', 'Leads - Havor Admin')
@section('page-title', 'Leads Management')

@section('content')
<div class="bg-white rounded-lg card-shadow p-8">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
    <h3 class="font-semibold text-slate-700 text-lg flex items-center gap-2">
      <i class="bi bi-person-lines-fill"></i> All Leads
    </h3>
    <form class="flex flex-col md:flex-row gap-2 w-full md:w-auto" method="GET" action="{{ route('admin.leads.index') }}">
      <input type="text" name="search" class="rounded-lg border border-slate-200 px-4 py-2 bg-slate-50 focus:border-sky-500 outline-none w-full md:w-48" placeholder="Search..." value="{{ request('search') }}">
      <select name="status" class="rounded-lg border border-slate-200 px-4 py-2 bg-slate-50 focus:border-sky-500 outline-none w-full md:w-40">
        <option value="">All Status</option>
        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
        <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
      </select>
      <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded shadow hover:bg-sky-700 transition w-full md:w-auto">
        <i class="bi bi-search"></i>
      </button>
    </form>
  </div>

  <div>
    @if($leads->count() > 0)
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="py-2 text-left text-xs font-semibold text-slate-500">Name</th>
              <th class="py-2 text-left text-xs font-semibold text-slate-500">Email</th>
              <th class="py-2 text-left text-xs font-semibold text-slate-500">Company</th>
              <th class="py-2 text-left text-xs font-semibold text-slate-500">Status</th>
              <th class="py-2 text-left text-xs font-semibold text-slate-500">Notes</th>
              <th class="py-2 text-left text-xs font-semibold text-slate-500">Submitted At</th>
              <th class="py-2 text-center text-xs font-semibold text-slate-500">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($leads as $lead)
              <tr class="hover:bg-slate-50 transition">
                <td class="py-3 font-medium text-slate-700">{{ $lead->name }}</td>
                <td class="py-3 text-slate-500">{{ $lead->email }}</td>
                <td class="py-3 text-slate-500">{{ $lead->company }}</td>
                <td class="py-3">
                  <span
                    class="inline-block px-2 py-0.5 rounded text-xs font-semibold text-white
                      @if($lead->status == 'new') bg-sky-500
                      @elseif($lead->status == 'contacted') bg-amber-500
                      @elseif($lead->status == 'qualified') bg-green-500
                      @elseif($lead->status == 'converted') bg-cyan-600
                      @elseif($lead->status == 'closed') bg-slate-400
                      @else bg-slate-500 @endif"
                  >{{ ucfirst($lead->status) }}</span>
                </td>
                <td class="py-3 text-slate-500 max-w-[280px] truncate">{{ Str::limit($lead->notes, 50) }}</td>
                <td class="py-3 text-slate-500">{{ $lead->created_at->format('Y-m-d H:i') }}</td>
                <td class="py-3 text-center">
                  <a href="{{ route('admin.leads.edit', $lead) }}" class="inline-flex items-center justify-center px-2 py-1 border rounded text-amber-500 bg-white hover:bg-amber-50 me-1" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <button type="button"
                    class="inline-flex items-center justify-center px-2 py-1 border rounded text-red-600 bg-white hover:bg-red-50"
                    title="Delete"
                    onclick="showDeleteModal('{{ $lead->id }}', '{{ addslashes($lead->name) }}')">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="flex justify-center mt-6">
        {{ $leads->appends(request()->query())->links() }}
      </div>
    @else
      <div class="flex flex-col items-center py-10">
        <i class="bi bi-person-lines-fill text-slate-300 mb-4" style="font-size: 4rem;"></i>
        <h4 class="text-slate-400 font-semibold mb-1">No Leads Found</h4>
        <p class="text-slate-400">Try adjusting your filters or come back later.</p>
      </div>
    @endif
  </div>
</div>

<!-- Modal Delete Confirmation -->
<div
  id="deleteModalLead"
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
      Are you sure you want to delete lead <span id="deleteLeadName" class="font-semibold text-red-500"></span>?
      <div class="mt-1 text-xs text-slate-500">This action cannot be undone.</div>
    </div>
    <div class="flex gap-2 pt-4">
      <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition w-full justify-center" onclick="hideDeleteModal()">Cancel</button>
      <form id="deleteLeadForm" method="POST" class="w-full flex justify-center">
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
function showDeleteModal(leadId, leadName) {
  document.getElementById('deleteModalLead').classList.remove('hidden');
  document.getElementById('deleteLeadName').innerText = '"' + leadName + '"';
  var form = document.getElementById('deleteLeadForm');
  form.action = "{{ route('admin.leads.destroy', '__id__') }}".replace('__id__', leadId);
}
function hideDeleteModal() {
  document.getElementById('deleteModalLead').classList.add('hidden');
}
</script>
@endsection
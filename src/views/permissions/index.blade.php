@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Permissions</h1>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-primary btn-sm float-right" href="{{ route('permissions.create') }}">
                    <i class="fas fa-plus mr-1"></i>Add Permission
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('permissionsUi::layouts.messages')

        {{-- Hidden bulk action form --}}
        <form id="bulk-form" method="POST" action="{{ route('permissions.bulk-action') }}" style="display:none;">
            @csrf
            <input type="hidden" name="action" id="bulk-action-input">
            <div id="bulk-ids-container"></div>
        </form>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h3 class="card-title">
                            <i class="fas fa-key mr-2"></i>{{ count($permissions) }} Permissions
                        </h3>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="btn-group btn-group-sm" id="bulk-actions" style="display:none;">
                            <span class="btn btn-default disabled" id="selected-count">0 selected</span>
                            <button type="button" class="btn btn-danger" onclick="submitBulk('delete')">
                                <i class="fas fa-trash mr-1"></i>Delete Selected
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm float-right" style="max-width: 300px;">
                            <input type="text" id="search-input" class="form-control" placeholder="Search permissions...">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="searchable-table">
                    <thead>
                        <tr>
                            <th width="40px"><input type="checkbox" id="select-all"></th>
                            <th>Name</th>
                            <th width="120px">Guard</th>
                            <th width="120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td><input type="checkbox" value="{{ $permission->id }}" class="row-check"></td>
                            <td><code>{{ $permission->name }}</code></td>
                            <td><span class="badge badge-secondary">{{ $permission->guard_name }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-secondary" href="{{ route('permissions.edit', $permission->id) }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form class="d-inline" action="{{ route('permissions.destroy', $permission->id) }}" method="post" onsubmit="return confirm('Delete this permission?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
// Front-end search
document.getElementById('search-input').addEventListener('keyup', function() {
    var filter = this.value.toLowerCase();
    document.querySelectorAll('#searchable-table tbody tr').forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
    });
});

// Bulk actions
var selectAll = document.getElementById('select-all');
var checkboxes = document.querySelectorAll('.row-check');
var bulkActions = document.getElementById('bulk-actions');
var selectedCount = document.getElementById('selected-count');

function updateBulkUI() {
    var count = document.querySelectorAll('.row-check:checked').length;
    bulkActions.style.display = count > 0 ? '' : 'none';
    selectedCount.textContent = count + ' selected';
}

selectAll.addEventListener('change', function() {
    checkboxes.forEach(function(cb) { cb.checked = selectAll.checked; });
    updateBulkUI();
});

checkboxes.forEach(function(cb) {
    cb.addEventListener('change', updateBulkUI);
});

function submitBulk(action) {
    if (action === 'delete' && !confirm('Delete selected permissions?')) return;
    var container = document.getElementById('bulk-ids-container');
    container.innerHTML = '';
    document.querySelectorAll('.row-check:checked').forEach(function(cb) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
    document.getElementById('bulk-action-input').value = action;
    document.getElementById('bulk-form').submit();
}
</script>
@endsection

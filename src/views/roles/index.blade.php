@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Roles</h1>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-primary btn-sm float-right" href="{{ route('roles.create') }}">
                    <i class="fas fa-plus mr-1"></i>Add Role
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('permissionsUi::layouts.messages')

        {{-- Hidden bulk action form --}}
        <form id="bulk-form" method="POST" action="{{ route('roles.bulk-action') }}" style="display:none;">
            @csrf
            <input type="hidden" name="action" id="bulk-action-input">
            <div id="bulk-ids-container"></div>
        </form>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h3 class="card-title">
                            <i class="fas fa-user-tag mr-2"></i>{{ $roles->total() }} Roles
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
                            <input type="text" id="search-input" class="form-control" placeholder="Search roles...">
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
                            <th width="60px">ID</th>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th width="220px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td><input type="checkbox" value="{{ $role->id }}" class="row-check"></td>
                            <td>{{ $role->id }}</td>
                            <td><strong>{{ $role->name }}</strong></td>
                            <td>
                                <span class="badge badge-info">{{ $role->permissions->count() }} permissions</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-info" href="{{ route('roles.show', $role->id) }}" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-secondary" href="{{ route('roles.edit', $role->id) }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('roles.clone', $role->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button class="btn btn-outline-primary" title="Clone Role"><i class="fas fa-clone"></i></button>
                                    </form>
                                    <form class="d-inline" action="{{ route('roles.destroy', $role->id) }}" method="post" onsubmit="return confirm('Delete this role?')">
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
            <div class="card-footer clearfix">
                {{ $roles->links() }}
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
    if (action === 'delete' && !confirm('Delete selected roles?')) return;
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

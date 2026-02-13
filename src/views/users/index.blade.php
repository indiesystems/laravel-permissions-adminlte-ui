@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Users</h1>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-primary btn-sm float-right" href="{{ route('users.create') }}">
                    <i class="fas fa-plus mr-1"></i>Create User
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @include('permissionsUi::layouts.messages')

        {{-- Filters --}}
        <div class="card card-outline card-secondary collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filters</h3>
                <div class="card-tools">
                    @if(request()->hasAny(['search', 'role', 'verified', 'status']))
                        <a href="{{ route('users.index') }}" class="btn btn-tool text-danger" title="Clear filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or email..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control form-control-sm">
                                    <option value="">All roles</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Verified</label>
                                <select name="verified" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified</option>
                                    <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified</option>
                                </select>
                            </div>
                        </div>
                        @if(config('permissions-ui.features.user_status'))
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    @foreach(config('permissions-ui.user_statuses', []) as $s)
                                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-search mr-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Hidden bulk action form (outside table, submitted via JS) --}}
        <form id="bulk-form" method="POST" action="{{ route('users.bulk-action') }}" style="display:none;">
            @csrf
            <input type="hidden" name="action" id="bulk-action-input">
            <input type="hidden" name="bulk_role" id="bulk-role-input">
            <div id="bulk-ids-container"></div>
        </form>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-2"></i>{{ $users->total() }} Users
                        </h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group btn-group-sm" id="bulk-actions" style="display:none;">
                            <span class="btn btn-default disabled" id="selected-count">0 selected</span>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cog mr-1"></i>Bulk Actions
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item text-warning" href="#" onclick="submitBulk('assign_role')">
                                        <i class="fas fa-user-tag mr-1"></i>Assign Role...
                                    </a>
                                    <a class="dropdown-item text-success" href="#" onclick="submitBulk('verify')">
                                        <i class="fas fa-check mr-1"></i>Verify Email
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" onclick="submitBulk('delete')">
                                        <i class="fas fa-trash mr-1"></i>Delete Selected
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="40px"><input type="checkbox" id="select-all"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Verified</th>
                            @if(config('permissions-ui.features.user_status'))
                            <th>Status</th>
                            @endif
                            <th>Roles</th>
                            <th width="280px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td><input type="checkbox" value="{{ $user->id }}" class="row-check"></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->hasVerifiedEmail())
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Verified</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Unverified</span>
                                @endif
                            </td>
                            @if(config('permissions-ui.features.user_status'))
                            <td>
                                @php $statusColors = ['active' => 'success', 'suspended' => 'warning', 'banned' => 'danger']; @endphp
                                <span class="badge badge-{{ $statusColors[$user->status] ?? 'secondary' }}">{{ ucfirst($user->status ?? 'active') }}</span>
                            </td>
                            @endif
                            <td>
                                @foreach($user->getRoleNames() as $role)
                                    <span class="badge badge-primary">{{ $role }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm mr-1">
                                    <a class="btn btn-info" href="{{ route('users.show', $user->id) }}" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-secondary" href="{{ route('users.edit', $user->id) }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    @if(config('permissions-ui.features.impersonation') && auth()->id() !== $user->id && !session('impersonate_original_id'))
                                    <form action="{{ route('users.impersonate.start', $user->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button class="btn btn-outline-dark" title="Impersonate"><i class="fas fa-user-secret"></i></button>
                                    </form>
                                    @endif
                                    <form action="{{ route('users.manual-resend-verification', $user->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button class="btn btn-outline-primary" title="Resend Verification"><i class="fas fa-paper-plane"></i></button>
                                    </form>
                                    <form action="{{ route('users.manual-reset-password', $user->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button class="btn btn-outline-warning" title="Reset Password"><i class="fas fa-key"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $users->links() }}
            </div>
        </div>

        {{-- Modal for bulk role assignment --}}
        <div class="modal fade" id="bulk-role-modal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Role</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <select class="form-control" id="modal-role-select">
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="confirmBulkRole()">Assign</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
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

function collectIds() {
    var container = document.getElementById('bulk-ids-container');
    container.innerHTML = '';
    document.querySelectorAll('.row-check:checked').forEach(function(cb) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
}

function submitBulk(action) {
    if (action === 'assign_role') {
        $('#bulk-role-modal').modal('show');
        return;
    }
    if (action === 'delete' && !confirm('Delete selected users?')) return;

    collectIds();
    document.getElementById('bulk-action-input').value = action;
    document.getElementById('bulk-form').submit();
}

function confirmBulkRole() {
    collectIds();
    document.getElementById('bulk-role-input').value = document.getElementById('modal-role-select').value;
    document.getElementById('bulk-action-input').value = 'assign_role';
    $('#bulk-role-modal').modal('hide');
    document.getElementById('bulk-form').submit();
}
</script>
@endsection

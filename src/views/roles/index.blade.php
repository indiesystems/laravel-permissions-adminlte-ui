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

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title">
                            <i class="fas fa-user-tag mr-2"></i>{{ $roles->total() }} Roles
                        </h3>
                    </div>
                    <div class="col-md-6">
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
                            <th width="60px">ID</th>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th width="180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($roles as $role)
                        <tr>
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
document.getElementById('search-input').addEventListener('keyup', function() {
    var filter = this.value.toLowerCase();
    var rows = document.querySelectorAll('#searchable-table tbody tr');
    rows.forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
    });
});
</script>
@endsection

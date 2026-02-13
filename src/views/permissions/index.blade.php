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

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title">
                            <i class="fas fa-key mr-2"></i>{{ count($permissions) }} Permissions
                        </h3>
                    </div>
                    <div class="col-md-6">
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
                            <th>Name</th>
                            <th width="120px">Guard</th>
                            <th width="120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $permission)
                        <tr>
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
document.getElementById('search-input').addEventListener('keyup', function() {
    var filter = this.value.toLowerCase();
    var rows = document.querySelectorAll('#searchable-table tbody tr');
    rows.forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
    });
});
</script>
@endsection

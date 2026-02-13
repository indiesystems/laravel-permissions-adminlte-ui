@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add New Role</h1>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-default btn-sm float-right" href="{{ route('roles.index') }}">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('roles.store') }}">
            @csrf

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i>Role Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input value="{{ old('name') }}"
                            type="text"
                            class="form-control"
                            name="name"
                            placeholder="Role name" required>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title"><i class="fas fa-key mr-2"></i>Assign Permissions</h3>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm float-right" style="max-width: 300px;">
                                <input type="text" id="permission-search" class="form-control" placeholder="Search permissions...">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover" id="permissions-table">
                        <thead>
                            <tr>
                                <th width="40px"><input type="checkbox" name="all_permission" id="select-all"></th>
                                <th>Name</th>
                                <th width="100px">Guard</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($permissions as $permission)
                            <tr>
                                <td>
                                    <input type="checkbox"
                                        name="permission[{{ $permission->name }}]"
                                        value="{{ $permission->name }}"
                                        class="permission">
                                </td>
                                <td><code>{{ $permission->name }}</code></td>
                                <td><span class="badge badge-secondary">{{ $permission->guard_name }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save role</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('select-all').addEventListener('click', function() {
    var checked = this.checked;
    document.querySelectorAll('#permissions-table .permission').forEach(function(cb) {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = checked;
        }
    });
});

document.getElementById('permission-search').addEventListener('keyup', function() {
    var filter = this.value.toLowerCase();
    document.querySelectorAll('#permissions-table tbody tr').forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
    });
});
</script>
@endsection

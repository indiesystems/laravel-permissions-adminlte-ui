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

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-2"></i>{{ $users->total() }} Users
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-sm float-right" style="max-width: 300px;">
                            <input type="text" id="search-input" class="form-control" placeholder="Search users...">
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
                            <th>Email</th>
                            <th>Verified</th>
                            <th>Roles</th>
                            <th width="220px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->hasVerifiedEmail())
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Verified</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Unverified</span>
                                @endif
                            </td>
                            <td>
                                @foreach($user->getRoleNames() as $role)
                                    <span class="badge badge-primary">{{ $role }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-info" href="{{ route('users.show', $user->id) }}" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-secondary" href="{{ route('users.edit', $user->id) }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('users.manual-resend-verification', $user->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button class="btn btn-primary" title="Resend Verification"><i class="fas fa-paper-plane"></i></button>
                                    </form>
                                    <form action="{{ route('users.manual-reset-password', $user->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <button class="btn btn-warning" title="Reset Password"><i class="fas fa-key"></i></button>
                                    </form>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this user?')">
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
                {{ $users->links() }}
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

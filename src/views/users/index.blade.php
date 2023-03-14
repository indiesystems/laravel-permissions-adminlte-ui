@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h1>Users</h1>
                <div class="lead">
                    Manage your users.
                    <a class="btn btn-primary btn-sm float-right" href="{{ route('users.create') }}">Create</a>
                </div>
            </div><!-- /.col -->
        </div><!-- /.container-fluid -->
        <div class="mt-2">
            @include('permissionsUi::layouts.messages')
        </div>
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-0">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->getRoleNames() as $role)
                                                <span class="badge badge-primary">{{ $role }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="btn btn-info btn-sm" href="{{ route('users.show', $user->id) }}">View</a>
                                                <a class="btn btn-secondary btn-sm" href="{{ route('users.edit', $user->id) }}">Edit</a>
                                                <form action="{{ route('users.destroy', $user->id) }}" method="post" class="btn-group">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer clearfix">
                            {{ $users->links() }}
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection
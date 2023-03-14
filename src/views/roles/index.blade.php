@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="bg-light p-4 rounded">
        <h1>Roles</h1>
        <div class="lead">
            Manage your roles here.
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm float-right">Add role</a>
        </div>
        
        <div class="mt-2">
            @include('permissionsUi::layouts.messages')
        </div>
        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-striped table-valign-middle">
                  <tr>
                     <th>ID</th>
                     <th>Name</th>
                     <th>Action</th>
                  </tr>
                    @foreach ($roles as $key => $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-info btn-sm" href="{{ route('roles.show', $role->id) }}">Show</a>
                                <a class="btn btn-primary btn-sm" href="{{ route('roles.edit', $role->id) }}">Edit</a>
                                <form class="btn-group" action="{{ route('roles.destroy', $role->id) }}">
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        {!! $roles->links() !!}
    </div>
</div>
@endsection
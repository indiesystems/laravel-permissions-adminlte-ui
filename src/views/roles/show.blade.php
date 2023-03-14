@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left mb-2">
                <h2>{{ ucfirst($role->name) }} Role</h2>
                <h3>Assigned permissions</h3>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary btn-sm" href="{{ route('roles.index') }}"> Back</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mt-2">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col" width="20%">Name</th>
                                <th scope="col" width="1%">Guard</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rolePermissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->guard_name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info">Edit</a>
        <a href="{{ route('roles.index') }}" class="btn btn-default">Back</a>
    </div>
</div>
@endsection
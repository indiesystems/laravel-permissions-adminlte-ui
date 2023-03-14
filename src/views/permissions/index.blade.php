@extends('layouts.app')

@section('content')
<div class="container">
    <div class="bg-light p-4 rounded">
        <h1>Permissions</h1>
        <div class="lead">
            Manage your permissions here.
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm float-right">Add permissions</a>
        </div>
        
        <div class="mt-2">
            @include('permissionsUi::layouts.messages')
        </div>

        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-striped table-valign-middle">
                    <thead>
                    <tr>
                        <th width="15%">Name</th>
                        <th >Guard</th> 
                        <th width="15%"> Action</th> 
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->guard_name }}</td>
                                <td>
                                    <div class="btn-group">
                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-info btn-sm">Edit</a>
                                    <form class="btn-group" action="{{ route('permissions.destroy', $permission->id) }}" method="post">
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
        </div>
        
    </div>
</div>
@endsection
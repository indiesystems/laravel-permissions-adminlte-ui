@extends('layouts.app')

@section('content')
<div class="container mb-2">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left mb-2">
                <h2>User details</h2>
            </div>
            <div class="pull-right mb-2">
                <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}"> Back</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><b>{{ $user->name }}</b></div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item "><span class="text-bold">Name: </span>{{ $user->name }}</li>
                        <li class="list-group-item "><span class="text-bold">e-mail: </span>{{ $user->email }}</li>
                        @if(isset($user->username))
                        <li class="list-group-item "><span class="text-bold">Username: </span>{{ $user->username }}</li>
                        @endif
                        <li class="list-group-item "><span class="text-bold">Created: </span>{{ $user->created_at }}</li>
                        <li class="list-group-item "><span class="text-bold">Updated: </span>{{ $user->updated_at }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('users.index') }}" class="btn btn-default">Back</a>
    </div>
</div>

@endsection
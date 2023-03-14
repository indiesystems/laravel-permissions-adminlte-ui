@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left mb-2">
                <h2>Update User</h2>
                <div class="lead">
                    Manage user, change role..
                </div>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary btn-sm" href="{{ route('roles.index') }}"> Back</a>
            </div>
        </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input value="{{ $user->name }}" 
                type="text" 
                class="form-control" 
                name="name" 
                placeholder="Name" required>

            @if ($errors->has('name'))
                <span class="text-danger text-left">{{ $errors->first('name') }}</span>
            @endif
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input value="{{ $user->email }}"
                type="email" 
                class="form-control" 
                name="email" 
                placeholder="Email address" required>
            @if ($errors->has('email'))
                <span class="text-danger text-left">{{ $errors->first('email') }}</span>
            @endif
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-control" name="role" required>
                <option value="">Select role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        {{ in_array($role->name, $userRole) 
                            ? 'selected'
                            : '' }} >{{ $role->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('role'))
                <span class="text-danger text-left">{{ $errors->first('role') }}</span>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update user</button>
        <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
    </form>
</div>
@endsection
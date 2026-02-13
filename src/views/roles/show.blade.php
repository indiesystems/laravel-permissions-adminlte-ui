@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ ucfirst($role->name) }} Role</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <a class="btn btn-secondary btn-sm" href="{{ route('roles.edit', $role->id) }}">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="{{ route('roles.clone', $role->id) }}" method="post" class="d-inline">
                        @csrf
                        <button class="btn btn-outline-primary btn-sm"><i class="fas fa-clone mr-1"></i>Clone</button>
                    </form>
                    <a class="btn btn-default btn-sm" href="{{ route('roles.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            {{-- Permissions Card --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-key mr-2"></i>{{ $rolePermissions->count() }} Permissions</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $grouped = $rolePermissions->groupBy(function ($perm) {
                                return explode('.', $perm->name)[0];
                            });
                        @endphp
                        @forelse($grouped as $group => $perms)
                            <div class="mb-3">
                                <strong>{{ $group }}</strong>
                                <div class="mt-1">
                                    @foreach($perms as $perm)
                                        <code class="mr-1">{{ $perm->name }}</code>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <span class="text-muted">No permissions assigned</span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Users with this Role --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users mr-2"></i>{{ $users->total() }} Users with this Role</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th width="80px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="{{ route('users.show', $user->id) }}" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No users have this role</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($users->hasPages())
                    <div class="card-footer clearfix">
                        {{ $users->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

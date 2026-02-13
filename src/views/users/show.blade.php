@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $user->name }}</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <a class="btn btn-secondary btn-sm" href="{{ route('users.edit', $user->id) }}">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <a class="btn btn-default btn-sm" href="{{ route('users.index') }}">
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
            {{-- User Info Card --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user mr-2"></i>User Details</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table">
                            <tr>
                                <th width="40%">Name</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            @if(isset($user->username))
                            <tr>
                                <th>Username</th>
                                <td>{{ $user->username }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Verified</th>
                                <td>
                                    @if($user->hasVerifiedEmail())
                                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i>{{ $user->email_verified_at->format('M d, Y') }}</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Unverified</span>
                                    @endif
                                </td>
                            </tr>
                            @if(config('permissions-ui.features.user_status') && isset($user->status))
                            <tr>
                                <th>Status</th>
                                <td>
                                    @php $statusColors = ['active' => 'success', 'suspended' => 'warning', 'banned' => 'danger']; @endphp
                                    <span class="badge badge-{{ $statusColors[$user->status] ?? 'secondary' }}">{{ ucfirst($user->status) }}</span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Created</th>
                                <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Updated</th>
                                <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Roles Card --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i>Roles</h3>
                    </div>
                    <div class="card-body">
                        @forelse($user->roles as $role)
                            <a href="{{ route('roles.show', $role->id) }}" class="badge badge-primary" style="font-size: 0.9em;">
                                {{ $role->name }}
                            </a>
                        @empty
                            <span class="text-muted">No roles assigned</span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Permissions Card --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-key mr-2"></i>Permissions ({{ $rolePermissions->count() + $directPermissions->count() }})</h3>
                    </div>
                    <div class="card-body">
                        @if($rolePermissions->count() > 0)
                            <h6 class="text-muted">Via Roles</h6>
                            @php
                                $grouped = $rolePermissions->groupBy(function ($perm) {
                                    return explode('.', $perm)[0];
                                });
                            @endphp
                            @foreach($grouped as $group => $perms)
                                <div class="mb-2">
                                    <strong>{{ $group }}</strong><br>
                                    @foreach($perms as $perm)
                                        <code class="mr-1">{{ $perm }}</code>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif

                        @if($directPermissions->count() > 0)
                            <hr>
                            <h6 class="text-muted">Direct Permissions</h6>
                            @foreach($directPermissions as $perm)
                                <code class="mr-1">{{ $perm }}</code>
                            @endforeach
                        @endif

                        @if($rolePermissions->count() === 0 && $directPermissions->count() === 0)
                            <span class="text-muted">No permissions</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

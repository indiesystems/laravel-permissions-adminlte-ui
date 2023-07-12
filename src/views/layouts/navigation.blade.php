@auth
  @role('admin')
    <li class="nav-item">
        <a href="{{ route('users.index') }}" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
                {{ __('Users') }}
            </p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('roles.index') }}" class="nav-link">
            <i class="nav-icon fas fa-user-tag"></i>
            <p>Roles</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('permissions.index') }}" class="nav-link">
            <i class="nav-icon fas fa-lock"></i>
            <p>Permissions</p>
        </a>
    </li>
    @endrole
@endauth

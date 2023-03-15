## Description

This is package will add role and permissions functionality with bootstrap4 ui, compatible with AdminLTE laravel theme. It also includes a command that will create permissions for each route name. Automatically registers the routes mentioned below plus all necessary views.

Resources registered:
* users
* roles
* permissions

Blade layouts provided:
* messages (session success messages for users,roles,permissions views)
* navigation (navigation menu links visible only to admin role)

## Requirements

You need to install spatie/laravel-permissions to use this package.

### Create a new laravel 9+ project 

`composer create-project laravel/laravel my-app`

### Install adminLTE theme with laravel UI

`composer require laraveldaily/larastarters --dev`
[more info for larastarters package](https://github.com/LaravelDaily/Larastarters)

Configure Larastarters, run the command below:

`php artisan larastarters:install`

Give 2 (Laravel UI - boostrap) and 1 (AdminLTE theme) as input to interactive menu.

Compile the project assets, run:

`npm install && npm run dev`
or for production use
`npm install && npm run build`


## Installation

`composer require indiesystems/laravel-permissions-adminlte-ui`

## Configuration

Add these entries to your `app\Http\Kernel.php` route middleware section like this:

```
    protected $routeMiddleware = [
		...
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \IndieSystems\PermissionsAdminlteUi\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    ];
```

Note that we are using a custom PermissionMiddleware and not spatie's.

**The Service Provider will be autoloaded via laravel autodiscovery.**

### User model configuration

Add Spatie HasRoles Trait to your user model

```
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, ... HasRoles;
```

### Navigation menu entries

You need to add to your AdminLTE `resources/views/layouts/navigation.blade.php` the following line, inside `nav > ul` tag

`@include('permissionsUi::layouts.navigation')`

## Create Permissions Command

This will create all permissions based on route names.
Note: You should specify permissions middleware on each controller constructor

`php artisan permission:create-permission-routes`

## Create basic roles && assign admin

This command will create `admin` and `user` roles. Admin role will be assigned with all permissions and user role will have profile.list and profile.edit.

`php artisan permission:create-basic-roles`

`php artisan permission:assign-admin test@example.com`

## Permission mapping schema

Permission Name | Route(s)
* list   => ['index', 'show'],
* create => ['create', 'store'],
* edit   => ['edit', 'update'],
* delete => ['destroy'],

## Specify permsissions on controller level

```
class FooController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:foo.list|foo.create|foo.edit|foo.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:foo.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:foo.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:foo.delete', ['only' => ['destroy']]);
    }
```


## Use permission middleware on route or route group

Assigning permission middleware like this will not honor `*.list` permission (for now).

```
Route::middleware(['auth','permission'])->group(function () {
    Route::view('about', 'about')->name('about');
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::resource('post', \App\Http\Controllers\ConsumptionController::class);
});
```
<?php
namespace Veneridze\LaravelPermission\Traits;
use Illuminate\Database\Eloquent\Collection;
use Veneridze\LaravelPermission\Models\Role;
use Veneridze\LaravelPermission\Permission;
use App\Models\User;
trait HasAssignUsers {
    public function roles(): Collection {
        return Role::where('model_name', app(Permission::class)->getClassName($this::class))->get();
    }
    public function users(): Collection {
        return User::whereBelongsTo($this->roles())->get();
    }
}
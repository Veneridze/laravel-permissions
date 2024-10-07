<?php
namespace Veneridze\LaravelPermission\Traits;

use Illuminate\Database\Eloquent\Model;
use Veneridze\LaravelPermission\Interfaces\Assignable;
use Veneridze\LaravelPermission\Permission;
use Veneridze\LaravelPermission\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


trait HasRole {
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function relationModel(): Assignable | null {
        if(!$this->roleData->model_name || !$this->model_id) {
            return null;
        }
        $mod = app(Permission::class)->getClass($this->roleData->model_name);
        return $mod::find($this->model_id);
    }

    public function getRoleDataAttribute(): Role
    {
        return Role::findOrFail($this->role_id);
    }
}
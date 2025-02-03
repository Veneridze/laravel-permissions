<?php
namespace Veneridze\LaravelPermission\Policies;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Veneridze\LaravelPermission\Permission;

class GenericPolicy
{
    protected string $model;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $perm = app(Permission::class)->getClassName($this->model);
        return app(Permission::class)->can($user, "{$perm}.view");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        $perm = app(Permission::class)->getClassName($this->model);
        return

            app(Permission::class)->can($user, "{$perm}.view") && app(Permission::class)->canAccess($user, $model) ||
            (method_exists($model, 'isOwner') ? $model->isOwner($user) : false);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $perm = app(Permission::class)->getClassName($this->model);
        return app(Permission::class)->can($user, "{$perm}.create");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        $perm = app(Permission::class)->getClassName($this->model);
        return app(Permission::class)->can($user, "{$perm}.update") && app(Permission::class)->canAccess($user, $model) ||
            (method_exists($model, 'isOwner') ? $model->isOwner($user) : false);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        $perm = app(Permission::class)->getClassName($this->model);
        return app(Permission::class)->can($user, "{$perm}.delete") && app(Permission::class)->canAccess($user, $model) ||
            (method_exists($model, 'isOwner') ? $model->isOwner($user) : false);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }
}

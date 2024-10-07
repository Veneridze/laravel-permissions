<?php
namespace Veneridze\LaravelPermission\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Veneridze\LaravelPermission\Attributes\HasPermission;
use Veneridze\LaravelPermission\Permission;
#[HasPermission]

class Role extends Model
{
    protected $guarded = [];
    static string  $label = 'Роль';
    protected $table = 'roles';
    protected $casts = [
        'perms' => 'array'
    ];

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }

    public function relationModel(): string | null {
        if(!$this->model_name) {
            return null;
        }
        return app(Permission::class)->getClass($this->model_name);
    }
}

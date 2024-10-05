<?php
namespace Veneridze\LaravelPermission\Models;


use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Veneridze\LaravelPermission\Attributes\HasPermission;
#[HasPermission]

class Role extends Model
{
    //use HasLogs;
    protected $guarded = [];
    static string  $label = 'Роль';
    protected $table = 'roles';
    protected $casts = [
        'perms' => 'array'
    ];

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}

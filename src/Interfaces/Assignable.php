<?php
namespace Veneridze\LaravelPermission\Interfaces;
use Illuminate\Database\Eloquent\Model;
interface Assignable {
    public function getAccessIds(string $model): array | bool;
    public function canAccess(Model $model): bool;
}
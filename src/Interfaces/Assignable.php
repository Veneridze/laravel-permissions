<?php
namespace Veneridze\LaravelPermission\Interfaces;
interface Assignable {
    public function getAccessIds(string $model): array | bool;
    public function canAccess(Model $model): bool;
}
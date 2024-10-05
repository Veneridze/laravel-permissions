<?php
namespace Veneridze\LaravelPermission\Validation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Veneridze\LaravelPermission\Permission;

class ValidPermission implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value)) {
            foreach ($value as $perm) {
                if (!app(Permission::class)->exist($perm)) {
                    $fail("Правило {$perm} не существует");
                }
            }
        } elseif (is_string($value)) {

            if (!app(Permission::class)->exist($value)) {
                $fail("Правило {$value} не существует");
            }
        } else {
            $fail("Некорректный тип данных аттрибута {$attribute}");
        }
    }
}
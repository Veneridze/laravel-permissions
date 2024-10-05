<?php
namespace Veneridze\LaravelPermission;


use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\ModelInfo\ModelInfo;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Attributes\Computed;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Veneridze\LaravelPermission\Attributes\CanAssign;
use Veneridze\LaravelPermission\Attributes\HasPermission;
use Veneridze\LaravelPermission\Exceptions\PermissionException;
use Veneridze\LaravelPermission\Models\Role;

class Permission implements Arrayable
{
    /**
     * Create a new class instance.
     */
    public array $permissions = [];

    public function toArray()
    {
        return $this->permissions;
    }
    public function getClassName(string $model): string
    {
        return strtolower(str_replace('\\', '.', $model));
    }

    public function getClass(string $model): string
    {
        return ModelInfo::forAllModels()
            ->filter(fn($mod) => strtolower(str_replace('\\', '.', $mod->class)) == $model)
            ->first()
            ->class;
    }

    public function getModelParameters(string $model): array
    {
        $info = ModelInfo::forModel($model);
        $result = [
            ...$info->attributes->filter(fn($attr) => $attr->name != 'id')->map(fn($attr) => $attr->name),
            ...$info->relations->map(fn($attr) => $attr->name)
        ];

        //throw new Exception($model);
        if (property_exists($model, 'form')) {
            $reflect = new ReflectionClass($model::$form);
            foreach ($reflect->getProperties() as $property) {
                //if($model == JobRequestData::class) {
                //    throw new Exception(count($property->getAttributes(Computed::class)));
                //}
                foreach ($property->getAttributes(Computed::class) as $attribute) {
                    //throw new Exception($property->getName());
                    if ($property->getName() != 'id') {
                        $result[] = strtolower($property->getName());
                    }
                }
            }
        }
        return array_unique($result);
    }

    public function getEditableModelParameters(string $model): array
    {
        $info = ModelInfo::forModel($model);
        return [
            ...$info->attributes->filter(fn($attr) => $attr->hidden == false && $attr->virtual == false && $attr->name != 'id' && $attr->name != 'created_at' && $attr->name != 'updated_at' && $attr->name != 'deleted_at')->map(fn($attr) => $attr->name),
            ...$info->relations->filter(fn($rel) => $rel->type != BelongsTo::class)->map(fn($attr) => $attr->name)
        ];
    }

    //public function getWriteParameters(string $model): array {
    //    $info = ModelInfo::forModel($model);
    //    return [
    //        ...$info->attributes->map(fn($attr) => $attr->name), 
    //        ...$info->relations->map(fn($attr) => $attr->name)
    //    ];
    //    
    //}

    public function getInfo()
    {
        return $this->getPermissionModels()
            ->map(fn($mod) => [
                'class' => $this->getClassName($mod->class),
                'className' => $mod->class,
                'label' => $mod->class::$label,
                'fields' => $mod->attributes
                    //->filter(fn($attr) => $attr->virtual == false)
                    ->map(fn($attr) => [
                        'name' => $attr->name,
                        'virtual' => $attr->virtual
                    ]),
                'relations' => $mod->relations
                    ->filter(fn($rel): bool => $this->isPermissionModel($rel->related))
                    ->map(fn($rel): array => [
                        'name' => $rel->name,
                        'label' => $rel->related::$label
                    ])

            ]);
    }

    public function __construct()
    {
        $data = $this->getInfo();

        $result = config('permission.extend_single_rules', []);
        foreach ($data as $mod) {

            $class = $mod['className'];
            $classspace = strtolower($mod['class']);
            
            $result[$classspace] = [
                ...[
                    "view" => 'Просматривать ' . $mod['label'],
                    "update" => 'Редактировать ' . $mod['label'],
                    "create" => 'Создавать ' . $mod['label'],
                    "delete" => 'Удалять ' . $mod['label']
                ],
                ...config('permission.extend_model_rules', []),
                ...config('permission.extend_specific_model_rules')($class)
            ];

            foreach ($mod['fields'] as $field) {
                if (!in_array($field, config('permission.exclude_fields'))) {
                    $fieldname = strtolower($field['name']);
                    $result[$classspace]["view"][$fieldname] = "Просматривать {$fieldname}";
                    if (!$field['virtual']) {
                        $result[$classspace]["update"][$fieldname] = "Редактировать {$fieldname}";
                        $result[$classspace]["create"][$fieldname] = "Создавать {$fieldname}";
                    }
                }
            }

            foreach ($mod['relations'] as $field) {
                if (!in_array($field, config('permission.exclude_fields'))) {
                    $fieldname = strtolower($field['name']);
                    $result[$classspace]["view"][$fieldname] = 'Просматривать ' . $field['label'];
                    $result[$classspace]["update"][$fieldname] = 'Редактировать ' . $field['label'];
                    $result[$classspace]["create"][$fieldname] = 'Создавать ' . $field['label'];
                }
            }
        }

        $this->permissions = $result;
    }

    public function exist(string $perm): bool
    {
        return array_key_exists($perm, $this->permissions);
    }

    public function can(Model $user, string $perm)
    {
        if (!$this->exist(strtolower($perm))) {
            throw new PermissionException("Право {$perm} не существует");
        }
        return $this->canRole($user->role['id'], strtolower($perm));
    }

    public static function canAccess($user, Model $model): bool
    {
        $rel = $user->relationModel();
        return $rel ? $rel->canAccess($model) : true;
    }

    public static function getAccessIds($user, string $model): array|bool
    {

        $rel = $user->relationModel();
        if ($rel) {
            try {
                return $rel->getAccessIds($model);
            } catch (Exception $e) {
                return [];
            }
        } else {
            return true;
        }
    }
    public function canRole(Role $role, string $perm): bool
    {
        return in_array(strtolower($perm), $role->perms ?? []);
    }

    public function getAssignModels(): Collection
    {
        return ModelInfo::forAllModels()
            ->filter(fn($model): bool => in_array(CanAssign::class, array_map(fn($attr) => $attr->getName(), (new ReflectionClass($model->class))->getAttributes())));
    }
    public function isPermissionModel(string $model): bool
    {
        return in_array(HasPermission::class, array_map(fn($attr) => $attr->getName(), (new ReflectionClass($model))->getAttributes()));
    }
    public function getPermissionModels(): Collection
    {
        return ModelInfo::forAllModels()->filter(fn($model): bool => $this->isPermissionModel($model->class)); 
    }
}
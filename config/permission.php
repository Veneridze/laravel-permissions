<?php
use Spatie\LaravelData\Attributes\Computed;

return [
    'extend_single_rules' => [
        'logs' => 'Просматривать логи'
    ],
    'extend_model_rules' => [],
    'exclude_fields' => [
        'id', 'created_at', 'updated_at'
    ],
    'extend_specific_model_rules' => function ($class): array | null {
        $result = [];
        if (property_exists($class, 'form')) {
            $result['view'] = [];
            $reflect = new ReflectionClass($class::$form);
            foreach ($reflect->getProperties() as $property) {

                foreach ($property->getAttributes(Computed::class) as $attribute) {
                    $result["view"][strtolower($property->getName())] = 'Просматривать вычисляемое свойство ' . $property->getName();
                }
            }
        }

        if (property_exists($class, 'docs')) {
            $result['doc'] = [];
            foreach ($class::$docs as $property) {
                    $name = strtolower(basename($property));
                    $result['doc'][$name] = "Генерировать документ ".$property::$label; 
            }
        }

        if (property_exists($class, 'actions')) {
            $result['action'] = [];
            foreach ($class::$actions as $property) {
                    $name = strtolower(basename($property));
                    $result['action'][$name] = "Выполнять действие ".$property::$label; 
            }
        }
        return $result;
    }
];
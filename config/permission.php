<?php

return [
    'extend_single_rules' => [
        
    ],
    'extend_model_rules' => [],
    'exclude_fields' => [
        "view" => [],
        "update" => ['id', 'created_at', 'updated_at'],
        "create" => ['id', 'created_at', 'updated_at']
    ],
    'extend_specific_model_rules' => function ($class): array {
        return [];
    },

    'extend_specific_model_fields' => function ($class): array {
        return [];
    }
];
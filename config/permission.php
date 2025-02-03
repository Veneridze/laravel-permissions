<?php

return [
    'webmaster' => env('WEBMASTER_EMAIL', null),
    'extend_single_rules' => [

    ],
    'extend_model_rules' => [],
    'exclude_fields' => [
        "view" => [],
        "update" => ['id', 'created_at', 'updated_at', 'created_by', 'updated_by'],
        "create" => ['id', 'created_at', 'updated_at', 'created_by', 'updated_by']
    ],
    // 'extend_specific_model_rules' => null,
    // 'extend_specific_model_fields' => null
];
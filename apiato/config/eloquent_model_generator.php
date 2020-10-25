<?php

return [
    'model_defaults' => [
        'namespace' => 'App\\Containers\\CONTAINERNAME\\Models',
        'base_class_name' => \App\Containers\Geral\Models\MainModel::class,
        'output_path' => null,
        'no_timestamps' => null,
        'date_format' => null,
        'connection' => null,
        'backup' => null,
    ],
    'db_types' => [
        'enum' => 'string',
        'json' => 'string',
    ]
];

<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/removido', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_removido_obter_todos',
    'uses' => $_localRouterConfigs['controller'] .'@indexRemovido',
    'middleware' => [
        'auth.admin'
    ],
]));

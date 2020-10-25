<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/removido/listas', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_removido_obter_listas',
    'uses' => $_localRouterConfigs['controller'] . '@obterListas',
    'middleware' => [
        'auth.admin'
    ],
]));

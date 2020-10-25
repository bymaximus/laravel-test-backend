<?php
$_localRouterConfigs = require __DIR__ . '/configs.php';


/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'], array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_todos',
    'uses' => $_localRouterConfigs['controller'] .'@index',
    'middleware' => [
        'auth.admin'
    ],
]));

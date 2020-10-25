<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->post($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'], array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_adicionar',
    'uses' => $_localRouterConfigs['controller'] .'@adicionar',
    'middleware' => [
        'auth.admin'
    ],
]));

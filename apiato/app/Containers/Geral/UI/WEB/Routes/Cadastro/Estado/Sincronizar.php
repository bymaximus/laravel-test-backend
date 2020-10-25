<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->post($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/sincronizar', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_sincronizar',
    'uses' => $_localRouterConfigs['controller'] .'@sincronizar',
    'middleware' => [
        'auth.admin'
    ],
]));

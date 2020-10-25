<?php
$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/funcionalidades', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_funcionalidades',
    'uses' => $_localRouterConfigs['controller'] .'@obterFuncionalidades',
    'middleware' => [
        'auth.admin'
    ],
]));

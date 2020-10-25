<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/perfil', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_perfis',
    'uses' => $_localRouterConfigs['controller'] .'@obterPerfis',
    'middleware' => [
        'auth.admin'
    ],
]));

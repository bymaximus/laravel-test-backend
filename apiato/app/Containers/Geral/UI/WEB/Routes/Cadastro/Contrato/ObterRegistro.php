<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/{id}', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_registro',
    'uses' => $_localRouterConfigs['controller'] .'@obterRegistro',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

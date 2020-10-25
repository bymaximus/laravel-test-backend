<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/visualizar/{id}', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_visualizar_registro',
    'uses' => $_localRouterConfigs['controller'] .'@obterVisualizarRegistro',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

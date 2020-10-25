<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/{id}/historico', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_historico_obter_todos',
    'uses' => $_localRouterConfigs['controller'] .'@indexHistorico',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->patch($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/{id}/url', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_atualizar_url',
    'uses' => $_localRouterConfigs['controller'] .'@atualizarUrl',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

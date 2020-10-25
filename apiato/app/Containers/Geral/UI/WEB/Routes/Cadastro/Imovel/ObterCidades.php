<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/cidades/{id}', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_cidades',
    'uses' => $_localRouterConfigs['controller'] . '@obterCidades',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

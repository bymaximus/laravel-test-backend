<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/{id_imovel}/cidades/{id}', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_registro_cidades',
    'uses' => $_localRouterConfigs['controller'] . '@obterRegistroCidades',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id_imovel', '[0-9]+')->where('id', '[0-9]+');

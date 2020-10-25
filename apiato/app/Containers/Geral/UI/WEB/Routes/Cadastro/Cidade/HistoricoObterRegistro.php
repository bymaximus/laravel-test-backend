<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';
/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/{id}/historico/{id_historico}', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_historico_obter_registro',
    'uses' => $_localRouterConfigs['controller'] .'@obterHistoricoRegistro',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+')->where('id_historico', '[0-9]+');

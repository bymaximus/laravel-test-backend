<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';


/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/removido/{id}/historico', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_removido_historico_obter_todos',
    'uses' => $_localRouterConfigs['controller'] .'@indexRemovidoHistorico',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

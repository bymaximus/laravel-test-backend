<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';


/** @var Route $router */
$router->patch($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/{id}/renomear', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_atualizar_nome',
    'uses' => $_localRouterConfigs['controller'] .'@atualizarNome',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

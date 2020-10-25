<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';


/** @var Route $router */
$router->patch($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/{id}/icone', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_atualizar_icone',
    'uses' => $_localRouterConfigs['controller'] .'@atualizarIcone',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/listaContratado', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_lista_contratado',
    'uses' => $_localRouterConfigs['controller'] . '@obterListaContratado',
    'middleware' => [
        'auth.admin'
    ],
]));

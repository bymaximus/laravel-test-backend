<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/listaDisponivel', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_lista_disponivel',
    'uses' => $_localRouterConfigs['controller'] . '@obterListaDisponivel',
    'middleware' => [
        'auth.admin'
    ],
]));

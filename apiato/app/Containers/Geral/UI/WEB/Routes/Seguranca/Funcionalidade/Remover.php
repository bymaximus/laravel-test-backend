<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';


/** @var Route $router */
$router->delete($_localRouterConfigs['adminUrlPrefix'] . '/'.$_localRouterConfigs['url'].'/{id}', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_remover',
    'uses' => $_localRouterConfigs['controller'] .'@remover',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');

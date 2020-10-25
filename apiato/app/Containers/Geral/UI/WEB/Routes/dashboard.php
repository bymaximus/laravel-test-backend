<?php

$adminRouterOptions = [];
$adminUrlPrefix = \Config::get('app.admin_url');

$router->get($adminUrlPrefix . '/dashboard', [
    'as' => 'get_admin_dashboard_page',
    'uses' => 'DashboardController@index',
]);

$router->get($adminUrlPrefix . '/{name}', [
    'as' => 'get_admin_dashboard_page',
    'uses' => 'DashboardController@index',
])->where('name', join('|', array_merge(
    [
        'unauthorized',
        'seguranca/perfil',
        'seguranca/perfil/funcionalidades',
        'seguranca/perfil/criar',
        'seguranca/perfil/editar/.*',
        'seguranca/perfil/historico/.*',
        'seguranca/perfil/removido',
        'seguranca/perfil/removido/.*',
        'seguranca/usuario',
        'seguranca/usuario/perfil',
        'seguranca/usuario/criar',
        'seguranca/usuario/editar/.*',
        'seguranca/usuario/historico/.*',
        'seguranca/usuario/removido',
        'seguranca/usuario/removido/.*',
        'seguranca/funcionalidade',

        'cadastro/estado',
        'cadastro/estado/criar',
        'cadastro/estado/editar/.*',
        'cadastro/estado/historico/.*',
        'cadastro/estado/removido',
        'cadastro/estado/removido/.*',
        'cadastro/cidade',
        'cadastro/cidade/criar',
        'cadastro/cidade/editar/.*',
        'cadastro/cidade/historico/.*',
        'cadastro/cidade/removido',
        'cadastro/cidade/removido/.*',

        'cadastro/imovel',
        'cadastro/imovel/criar',
        'cadastro/imovel/editar/.*',
        'cadastro/imovel/visualizar/.*',
        'cadastro/imovel/historico/.*',
        'cadastro/imovel/removido',
        'cadastro/imovel/removido/.*',

        'cadastro/contrato',
        'cadastro/contrato/criar',
        'cadastro/contrato/editar/.*',
        'cadastro/contrato/historico/.*',
        'cadastro/contrato/removido',
        'cadastro/contrato/removido/.*',
    ]
)));

$adminUrlDomain = \Config::get('app.admin_domain');
if ($adminUrlDomain) {
    $adminUrlDomain .= '.' . parse_url(\Config::get('app.url'))['host'];
    if ($adminUrlDomain) {
        $adminRouterOptions['domain'] = $adminUrlDomain;
    }
}

$router->post($adminUrlPrefix . '/refresh-login', array_merge($adminRouterOptions, [
    'as' => 'post_admin_refresh_login',
    'uses' => 'DashboardController@refreshLogin',
    'middleware' => [
        'auth.admin'
    ],
]));

$router->post($adminUrlPrefix . '/logout', array_merge($adminRouterOptions, [
    'as' => 'post_admin_logout',
    'uses' => 'DashboardController@logout',
    'middleware' => [
        'auth.admin'
    ],
]));

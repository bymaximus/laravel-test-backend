<?php

$router->any('/admin', 'InstallController@index');
$router->any('/admin/index', 'InstallController@index');
$router->any('/admin/{action}', 'InstallController');

$adminRouterOptions = [];
$adminUrlPrefix = \Config::get('app.admin_url');
$adminRouterOptions['domain'] = parse_url(\Config::get('app.url'))['host'];

$router->get($adminUrlPrefix . '/', array_merge($adminRouterOptions, [
    'as' => 'get_admin_index',
    'uses' => 'Controller@index',
]));

$router->get($adminUrlPrefix . '/login', array_merge($adminRouterOptions, [
    'as' => 'get_admin_login',
    'uses' => 'Controller@loginPage',
]));

$adminUrlDomain = \Config::get('app.admin_domain');
if ($adminUrlDomain) {
    $adminUrlDomain .= '.' . parse_url(\Config::get('app.url'))['host'];
    if ($adminUrlDomain) {
        $adminRouterOptions['domain'] = $adminUrlDomain;
    }
}
$router->post($adminUrlPrefix . '/login', array_merge($adminRouterOptions, [
    'as' => 'post_admin_login',
    'uses' => 'Controller@login',
]));

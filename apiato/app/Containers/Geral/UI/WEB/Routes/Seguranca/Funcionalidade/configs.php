<?php

$adminRouterOptions = [];
$adminUrlPrefix = \Config::get('app.admin_url');
$adminUrlDomain = \Config::get('app.admin_domain');
if ($adminUrlDomain) {
    $adminUrlDomain .= '.' . parse_url(\Config::get('app.url'))['host'];
    if ($adminUrlDomain) {
        $adminRouterOptions['domain'] = $adminUrlDomain;
    }
}

return [
    'controller' => 'Seguranca\FuncionalidadeController',
    'url' => 'seguranca/funcionalidade',
    'name' => 'seguranca_funcionalidade',
    'adminRouterOptions' => $adminRouterOptions,
    'adminUrlPrefix' => $adminUrlPrefix
];

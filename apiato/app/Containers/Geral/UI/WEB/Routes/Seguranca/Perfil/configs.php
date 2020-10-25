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
    'controller' => 'Seguranca\PerfilController',
    'url' => 'seguranca/perfil',
    'name' => 'seguranca_perfil',
    'adminRouterOptions' => $adminRouterOptions,
    'adminUrlPrefix' => $adminUrlPrefix
];

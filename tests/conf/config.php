<?php 
$cfg = include 'sqlite.conf.php';

/*
 * **************************************************************************
 * Core
 * **************************************************************************
 */
$cfg['test'] = true;
$cfg['debug'] = true;
$cfg['timezone'] = 'Europe/Berlin';
$cfg['installed_apps'] = array(
    'Pluf',
    'User'
);
$cfg['app_base'] = '/testapp';
$cfg['tmp_folder'] = dirname(__FILE__) . '/../tmp';
$cfg['upload_path'] = __DIR__ . '/../storage/tenant';
$cfg['mimetype'] = 'text/html';

$cfg['secret_key'] = '5a8d7e0f2aad8bdab8f6eef725412850';
$cfg['middleware_classes'] = array(
    '\Pluf\Middleware\Session',
    '\User\Middleware\Session'
);
/*
 * **************************************************************************
 * Template engine
 * **************************************************************************
 */
$cfg['template_tags'] = array(
    // Buildin tags
    'aperm' => '\Pluf\Template\Tag\APerm',
    'cfg' => '\Pluf\Template\Tag\Cfg',
    'cycle' => '\Pluf\Template\Tag\Cycle',
    'firstof' => '\Pluf\Template\Tag\Firstof',
    'media' => '\Pluf\Template\Tag\MediaUrl',
    'getmsgs' => '\Pluf\Template\Tag\Messages',
    'now' => '\Pluf\Template\Tag\Now',
    'regroup' => '\Pluf\Template\Tag\Regroup',
    'amedia' => '\Pluf\Template\Tag\RmediaUrl',
    'aurl' => '\Pluf\Template\Tag\Rurl',
    'tenant' => '\Pluf\Template\Tag\Tenant',
    'url' => '\Pluf\Template\Tag\Url',
);

$cfg['templates_folder'] = array(
    dirname(__FILE__) . '/../templates'
);

/*
 * **************************************************************************
 * Tenants
 * **************************************************************************
 */
$cfg['tenant_root_level'] = 10;
$cfg['tenant_root_title'] = 'Tenant title';
$cfg['tenant_root_description'] = 'Default tenant in single mode';
$cfg['tenant_root_domain'] = 'pluf.ir';
$cfg['tenant_root_subdomain'] = 'www';
$cfg['tenant_root_validate'] = 1;

/*
 * **************************************************************************
 * Logger
 * **************************************************************************
 */
$cfg['log_level'] = \Pluf\Log::OFF;
$cfg['log_delayed'] = false;
$cfg['log_handler'] = '\Pluf\Log\Console';


/*
 * **************************************************************************
 * User
 * **************************************************************************
 */
//
// Default user avatar
//
$cfg['user_avatar_default'] = __DIR__ . '/avatar.svg';

return $cfg;

<?php

use Symfony\Component\HttpFoundation\Request;

error_reporting(E_ALL | E_STRICT);

date_default_timezone_set('EET');

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.

/*$X_FORWARDED_FOR = array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';

if (! preg_match("~^172\.28\.25\.\d+$~", $X_FORWARDED_FOR)
    && (isset($_SERVER['HTTP_CLIENT_IP'])
        || $X_FORWARDED_FOR
        || ! in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.');
}*/

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('stage', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

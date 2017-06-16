<?php

use Symfony\Component\HttpFoundation\Request;

error_reporting(E_ALL | E_STRICT);

date_default_timezone_set('EET');

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || ! in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

if (extension_loaded('apc')) {
    $loader = new Symfony\Component\ClassLoader\ApcClassLoader('sf2.nmtn.local', $loader);
    $loader->register(true);
}

require_once __DIR__.'/../app/AppKernel.php';
$kernel = new AppKernel('local', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

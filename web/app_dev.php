<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$client_ip = isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] :
    @$_SERVER['REMOTE_ADDR'];
if(!in_array($client_ip, array(
    '192.172.226.208', // bgpstream-app-1
    '192.172.226.78',  // rommie
    '192.172.226.97',  // gibi
    '192.172.226.172',  // chiara-mbp
    '132.249.100.142', // ama desktop
    '192.172.226.169', // alberto-mbp
    '127.0.0.1',
    'fe80::1',
    '::1'
))
) {
    header('HTTP/1.0 403 Forbidden');
    exit($_SERVER['REMOTE_ADDR'] .
         ' is not allowed to access this file. Check ' . basename(__FILE__) .
         ' for more information.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

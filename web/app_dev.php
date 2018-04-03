<?php
function asd($arr=false, $params=false) {
    if ($params) {
        $str = 'class="prered"';
        $strTime = 'class="timParams"';
    } else {
        $str = 'class="preblack"';
        $strTime = 'class="tim"';
    }

    echo "<style type=\"text/css\">
                div.tim {font-size:12px; color: blue;}
                div.timParams {font-size:12px; color: green;}
                div.prered {font-size:10px; color: red;}
                div.preblack {font-size:10px;  text-align: left;}
          </style>";
    echo "<div $strTime>";
    echo date("H:i:s"). substr((string)microtime(), 1, 6)."</br>";
    echo "</div>";

    echo "<div $str>";
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    echo "</div>";
}
function asdd($arr=false) {
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read https://symfony.com/doc/current/setup.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'], true) || PHP_SAPI === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require __DIR__.'/../vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

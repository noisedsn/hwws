<?php

$lifetime = 7*24*60*60;
$secure = ($_SERVER["HTTPS"]) ? true : false;
$httponly = true;
$samesite = 'lax';

ini_set('session.gc_maxlifetime', $lifetime);

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => $httponly,
    'samesite' => $samesite
]);

session_start();


<?php

$lifetime = 365*24*60*60;
$secure = ($_SERVER["HTTPS"]) ? true : false;
$httponly = true;
$samesite = 'lax';

ini_set('session.gc_maxlifetime', $lifetime);

$cookie_options = [
    'expires' => time() + $lifetime, 
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => $httponly,
    'samesite' => $samesite
];

session_name('WSSESSID');
session_start();
setcookie(session_name(), session_id(), $cookie_options);

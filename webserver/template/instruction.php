<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$base = dirname($protocol . "://" . $host . $uri) . '/';
?>

<p><?=t('setup.instruction')?></p><table>
<tr><td>WiFi name:</td> <td>Weather Sensor</td></tr>
<tr><td>WiFi pass:</td> <td>123456789</td></tr>
<tr><td><?=t('setup.browser')?></td> <td>192.168.4.1</td></tr>
<tr><td>Server URL:</td> <td><?=$base?></td></tr>
<tr><td>PIN code:</td> <td><?=$pin?></td></tr></table>


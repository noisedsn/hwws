<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?=$title?></title>
<meta name="description" content="Tiny DIY environmental sensor dashboard" />
<meta name="keywords" content="Tiny DIY environmental sensor dashboard" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="MobileOptimized" content="320"/>
<meta name="HandheldFriendly" content="true"/>

<link rel="icon" type="image/x-icon" sizes="any" href="assets/favicon/src/favicon.png" />
<link rel="stylesheet" type="text/css" href="assets/style.css?<?= filemtime('assets/style.css') ?>" media="screen,print" >

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/luxon/2.5.2/luxon.min.js"></script>
<script src="assets/cookies.js?<?= filemtime('assets/cookies.js') ?>"></script>
<script>
window.I18N = <?= json_encode(loadTranslations(), JSON_UNESCAPED_UNICODE) ?>;
const locales = <?= json_encode(listTranslations(), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="assets/i18n.js?<?= filemtime('assets/i18n.js') ?>"></script>
<!-- <script src="assets/chartjs-plugin-zoom.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-adapter-luxon/1.3.1/chartjs-adapter-luxon.umd.js"></script>
</head>

<body onload="document.getElementById('bg').style.opacity='1'">
<div id="bg"></div>


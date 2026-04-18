<?php
// Template helpers
function icon($name) {
    $path = __DIR__ . '/../assets/icons/'.$name.'.svg';
    if (is_file($path))
        return file_get_contents($path);
    
    return $name;
}

function listTranslations(): array
{
    static $options = [];
    $files = scandir(__DIR__ . "/../locales/");
    foreach ($files as $file) {
        if (str_contains($file, '.php')) {
            $locale = str_replace('.php', '', $file);
            $code = explode('-', $locale)[0];
            array_push($options, ["locale" => $locale, "code" => $code]);
        }
    }
    return $options;
}


function loadTranslations(string $lang = 'en-US'): array
{
    static $translations = [];

    if (!empty($_COOKIE['ws_lang']))
        $lang = htmlspecialchars($_COOKIE['ws_lang']);
    
    if (!isset($translations[$lang])) {
        $translations[$lang] = include __DIR__ . "/../locales/$lang.php";
        $translations[$lang]['locale'] = $lang;
    }

    return $translations[$lang];
}

function t(string $key, array $vars = []): string
{
    static $cache = [];

    if (empty($cache)) {
        $cache = loadTranslations();
    }

    $value = $cache;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $key; // fallback
        }
        $value = $value[$segment];
    }

    if (!is_string($value)) {
        return $key;
    }

    foreach ($vars as $k => $v) {
        $value = str_replace('{' . $k . '}', $v, $value);
    }

    return $value;
}



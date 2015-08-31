<?php
include_once __DIR__ . '/../Bootstrapper.php';

// register this test dir
\FdlDebug\Autoloader::register(__DIR__);

if (!function_exists('xdebug_is_enabled')) {
    function xdebug_is_enabled() {
        return true;
    }
}

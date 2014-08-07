<?php
# Provide a way to pass a config file to the bootstrap
# by initializing a global variable named $fdldebug_config_file
# Example:
#
# $fdldebug_config_file = array([...]); // see global.php for config sample
# include Bootstrapper.php // the bootstrapper will inject the config automatically

if (!isset($GLOBALS['fdldebug_config_file']) && !isset($fdldebug_config_file)) {
    $fdldebug_config_file = null; // needs to be explicitly set to null to avoid var undeclared warnings
} else {
    $fdldebug_config_file = isset($fdldebug_config_file)  ? $fdldebug_config_file : $GLOBALS['fdldebug_config_file'];
}

if (!class_exists('\FdlDebug\Bootstrap')) {
    // register the autoloader
    require_once __DIR__ . '/src/FdlDebug/Autoloader.php';
    \FdlDebug\Autoloader::register();

    if (class_exists('\FdlDebug\Bootstrap')) {
        if (!\FdlDebug\Bootstrap::isInitialized()) {
            \FdlDebug\Bootstrap::init($fdldebug_config_file);
        }
    } else {
        trigger_error('Cannot initialize bootstrap', E_USER_NOTICE);
    }
} else {
    if (!\FdlDebug\Bootstrap::isInitialized()) {
        \FdlDebug\Bootstrap::init($fdldebug_config_file);
    }
}

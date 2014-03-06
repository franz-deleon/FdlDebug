<?php
################# do not edit beyond this point ####################
$config = \FdlDebug\Bootstrap::getConfigs();

if (!file_exists($config['xdebug']['trace_output_dir'])) {
    mkdir($config['xdebug']['trace_output_dir'], 0777, true);
}

if (\FdlDebug\StdLib\Utility::isXDebugTraceStart()) {
    if (\FdlDebug\StdLib\Utility::isXDebugEnabled()) {
        foreach ($config['xdebug'] as $key => $val) {
            ini_set("xdebug.$key", $val);
        }
        xdebug_start_trace();
    }
}

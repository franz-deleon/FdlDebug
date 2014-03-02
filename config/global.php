<?php
# This config is the default global configuration definitions
# Do not edit this file! You can override the configurations in config/local.php
# or by returning an array of configurations to variable $fdldebug_config_file
return array(
    'xdebug' => array(
        'trace_log'  => '/tmp/fdldebug',        # Path for trace log file for XDEBUG
        'trace_file' => 'fdltrace',             # File name for XDEBUG trace file
    ),
    'writer'     => 'var_dump',                 # Default writer. Available writers ('var_dump'). Using FQNS also qualifies
    'conditions' => array('boolean', 'range'),  # Registered conditions. Using FQNS also qualifies
    'debug_prefixes'   => array('pr', 'print'), # Declaration of debug prefixes for callable methods. Example printDebugTrace()
    'debug_extensions' => array(),              # Extensions for FdlDebug. Use FQNS to declare new extension
);

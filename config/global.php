<?php
$xdebug_trace_log  = '/tmp/nav_debug/xdebug/trace'; # Path for trace log file for XDEBUG
$xdebug_trace_file = 'xtrace'; # File name for XDEBUG trace file

################# do not edit beyond this point ####################

define('XDEBUG_TRACE_LOG', $xdebug_trace_log);
define('XDEBUG_TRACE_FILE', $xdebug_trace_file);

if (!file_exists(XDEBUG_TRACE_LOG)) mkdir(XDEBUG_TRACE_LOG, 0777, true);

ob_start();
ini_set('xdebug.trace_output_dir', XDEBUG_TRACE_LOG);
ini_set('xdebug.trace_output_name', XDEBUG_TRACE_FILE);
ini_set('xdebug.collect_assignments', 1);
ini_set('xdebug.trace_enable_trigger', 1);
ini_set('xdebug.var_display_max_data', 1280);
ini_set('xdebug.var_display_max_depth', 8);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.collect_params', 2);
ini_set('xdebug.collect_return', 0);
ini_set('xdebug.show_mem_delta', 0);

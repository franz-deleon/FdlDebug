<?php
/**
 * These are proxy php functions to use for faster and easier debugging.
 */

use FdlDebug\Front;
use FdlDebug\Bootstrap;

######### Print functions ##########

/**
 * @see \FdlDebug\Debug::pr();
 * @param mixed $value
 */
function pr($value) {
    return Front::i()->pr($value);
}

/**
 * @see \FdlDebug\Debug::prDie();
 * @param mixed $value
 */
function prd($value = '') {
    return Front::i()->prDie($value);
}

/**
 * @see \FdlDebug\Debug::pr();
 * @param mixed $value
 */
function pr_now($value) {
    return Front::i()->pr($value);
}

/**
 * @see \FdlDebug\Debug::printGlobal();
 * @param string $type
 */
function pr_global($type = null)
{
    return Front::i()->printGlobal($type);
}

/**
 * @see \FdlDebug\Debug::printBackTrace();
 * @param boolean $show_vendor
 */
function pr_backtrace($show_vendor = false) {
    return Front::i()->printBackTrace($show_vendor);
}

/**
 * @see \FdlDebug\Debug::printFiles();
 * @param string $show_vendor
 */
function pr_files($show_vendor = false) {
    return Front::i()->printFiles($show_vendor);
}

###### XDebug function(s) ######

/**
 * @see \FdlDebug\Extension::printXdebugTracedVar
 * @param string  $search
 * @param boolean $show_vendor
 */
function prx_trace_var($search, $show_vendor = false) {
    $fdl_debug = Front::i();
    $fdl_debug->printXdebugTracedVar($search, $show_vendor);

    return $fdl_debug;
}

######### Conditions ###########

/**
 * @see \FdlDebug\Condition\Boolean::condBoolean()
 * @param boolean $condition
 */
function cond_bool($condition) {
    $fdl_debug = Front::i();
    $fdl_debug->condBoolean($condition);

    return $fdl_debug;
}

/**
 * @see \FdlDebug\Condition\LoopRange::loopRange()
 * @param int $start
 * @param int $length
 */
function cond_range($start, $length = null) {
    $fdl_debug = Front::i();
    $fdl_debug->setBacktraceFuncToSearch(__FUNCTION__);
    $fdl_debug->loopRange($start, $length);
    $fdl_debug->setBacktraceFuncToSearch(Front::BACKTRACE_FUNC_TO_SEARCH); // reset after use

    return $fdl_debug;
}

/**
 * @param void
 * @return Ambigous <\FdlDebug\Front, \FdlDebug\FdlDebug\Front>
 */
function cond_range_nested_end() {
    $fdl_debug = Front::i();
    $fdl_debug->setBacktraceFuncToSearch(__FUNCTION__);
    $fdl_debug->rangeNestedEnd();
    $fdl_debug->setBacktraceFuncToSearch(Front::BACKTRACE_FUNC_TO_SEARCH); // reset after use

    return $fdl_debug;
}

/**
 * @see \FdlDebug\Condition\LoopFrom::loopFrom()
 * @param string  $expression
 * @param int     $length
 */
function cond_from($expression, $length = null) {
    $fdl_debug = Front::i();
    $fdl_debug->setBacktraceFuncToSearch(__FUNCTION__);
    $fdl_debug->loopFrom($expression, $length);
    $fdl_debug->setBacktraceFuncToSearch(Front::BACKTRACE_FUNC_TO_SEARCH); // reset after use

    return $fdl_debug;
}

/**
 * @param void
 * @return Ambigous <\FdlDebug\Front, \FdlDebug\FdlDebug\Front>
 */
function cond_from_nested_end() {
    $fdl_debug = Front::i();
    $fdl_debug->loopFromNestedEnd();

    return $fdl_debug;
}

/**
 * @see \FdlDebug\Extension\LoopFrom::loopFromFlush()
 * @param void
 */
function cond_from_flush() {
    $fdl_debug = Front::i();
    $fdl_debug->loopFromFlush();

    return $fdl_debug;
}

############# Misc functions #############

/**
 * Retrieves the instance of fdl debug
 * @return Front
 */
function fd_i($writer = null) {
    return Front::i($writer);
}

/**
 * Retrieves the writer instance
 * @return \FdlDebug\Writer
 */
function fd_writer()
{
    return Front::i()->getWriter();
}

################## DO NOT EDIT BELLOW HERE #################

// We need to register the functions
$fdl_functions = get_defined_functions();
Bootstrap::setConfigs('function_names', $fdl_functions['user']);

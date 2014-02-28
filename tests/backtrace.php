<?php
class A
{
    public function __call($methodName, $args)
    {
        var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
    }
}

$a = new A;
$a->someNonExistingFunc();
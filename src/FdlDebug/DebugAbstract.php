<?php
namespace FdlDebug;

abstract class DebugAbstract
{
    /**
     * Runs a PHP native debug_backtrace
     * @param void
     * @return array
     */
    public function getBackTrace($offset = 0)
    {
        $backTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $this->cleanTrace($backTrace);

        if (is_integer($offset)) {
            for ($i = 1; $i <= $offset; ++$i) {
                array_shift($backTrace);
            }
        }

        return $backTrace;
    }

    /**
     * Runs a get_included_files() and filters result
     * @param void
     * @return array
     */
    public function getFileTrace()
    {
        $fileTrace = get_included_files();
        $files = array();
        foreach ($fileTrace as $key => $file) {
            if (strpos($file, '/Zend/') === false
                && strpos($file, '/nav_debug/') === false
                && strpos($file, 'phar:') === false
            ) {
                $files[$key]['order'] = ($key + 1);
                $files[$key]['file']  = $file;
            }
        }

        array_unshift($files, array('Called Order', 'File'));

        return $files;
    }

    /**
     * Slices a backtrace array result
     *
     * @param array  $traceArray              The target trace array
     * @param string $traceKey                The target backtrace key
     * @param string $traceValueToSearch      The target backtrace's key value
     * @param number $fromSearchedValueOffset The offset that starts from a successfully searched $traceValueToSearch
     * @param number $fromStartOffset         The offset that starts from the beginning of $traceArray
     * @return array
     */
    public function findTraceKeyAndSlice(
        $traceArray,
        $traceKey,
        $traceValueToSearch,
        $fromSearchedValueOffset = 0,
        $fromStartOffset = 0
    ) {
        if ($fromStartOffset > 0) {
            $traceArray = array_slice($traceArray, $fromStartOffset);
        }
        foreach ($traceArray as $key => $val) {
            if ($val[$traceKey] === $traceValueToSearch) {
                return array_slice($traceArray, ($key + $fromSearchedValueOffset));
            }
        }
        return $traceArray;
    }

    /**
     * Cleans a trace.
     * Avoids the include(), require(), etc..
     * @param array $trace
     */
    protected function cleanTrace(array &$trace)
    {
        $cleanedTrace = array();
        foreach ($trace as $val) {
            if ($val['function'] != 'require'
                && $val['function'] != 'require_once'
                && $val['function'] != 'include'
                && $val['function'] != 'include_once'
            ) {
                if (isset($val['file']) && strpos($val['file'], 'phar://') === 0) {
                    continue;
                }

                $cleanedTrace[] = $val;
            }
        }
        array_shift($cleanedTrace);

        $trace = $cleanedTrace;
    }
}
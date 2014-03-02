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
     * Is XDebug enabled?
     * @param void
     * @return boolean
     */
    public function isXDebugEnabled()
    {
        if (function_exists('xdebug_is_enabled')) {
            if (xdebug_is_enabled() !== true) {
                return false;
            }
        }
        return true;
    }

    /**
     * Parse the search string from the xdebug trace log
     * @param string $search
     * @return array
     */
    public function parseVariableFromXdebug($search)
    {
        $search = trim($search, '$ ');

        $traceFile = XDEBUG_TRACE_LOG . '/' . XDEBUG_TRACE_FILE . '.xt';
        $exec = "grep -i \"\\\$*>{$search}\\b\" "  . $traceFile;
        exec($exec, $output);
        $exec = "grep -i \"\\\${$search}\\b\" " . $traceFile;
        exec($exec, $output);

        $extra['variable'] = $search;
        $this->cleanXdebugTrace($output);
        $this->cleanTraceVar($output, $search);

        return $output;
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
     * Cleans the output of the result array of Xdebug
     * @param array $output
     * @param boolean $showZF Should we output the ZF library in the trace?
     * @return array
     */
    protected function cleanXdebugTrace(array &$trace, $showZF = false)
    {
        $cleanedOutput = array();
        foreach ($trace as $key => $val) {
            if (strpos($val, "nav_debug") === false && (strpos($val, 'library/Zend') === false || $showZF == true)) {
                $val = trim($val);
                $lastSpace = (int) strrpos($val, ' ');
                $fileNameWithLine = trim(substr($val, $lastSpace));
                $fileNameArray    = explode(":", $fileNameWithLine); // separate the file from linenumber

                if ($fileNameArray[0]) $cleanedOutput[$key]['file'] = $fileNameArray[0];
                if ($fileNameArray[1]) $cleanedOutput[$key]['line'] = $fileNameArray[1];
                $cleanedOutput[$key]['initialization'] = trim(substr($val, 0, $lastSpace), ' -=>.0123456789');
            }
        }
        $trace = $cleanedOutput;
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

    /**
     * Header to clean the trace_var array
     * @param array $contents
     * @param string $var
     */
    protected function cleanTraceVar(array &$contents, $var = '')
    {
        $cleanedContent = array();
        foreach ($contents as $key => $content) {
            $cleanedContent[$key]["file"] = $content['file'];
            $cleanedContent[$key]["line"] = $content['line'];

            if (!empty($var)) {
                preg_match_all("~(\\\${$var} = .*?)[;,\s\)\}]~i", $content['initialization'], $matches);
                if (!empty($matches[1])) {
                    $cleanedContent[$key]["var(\${$var}) assignment"] = $matches[1];
                } else {
                    $cleanedContent[$key]["var(\${$var}) assignment"] = 'see initialization';
                }
            }

            $initialization = preg_replace(array('~\\\t~', '~\\\n~', '~\\\~'), array(' ', ''), $content['initialization']);
            $initialization = preg_replace('~\s\s+~', ' ', $initialization);

            $cleanedContent[$key]["initialization"] = $initialization;
        }

        $contents = $cleanedContent;
    }
}
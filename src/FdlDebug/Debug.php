<?php
namespace FdlDebug;

use ErrorException;

class Debug
{
    public function __construct()
    {
        require_once dirname(__FILE__) . '/../../config/global.php';
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
     * Runs a PHP native debug_backtrace
     * @param void
     * @return array
     */
    public function getDebugTrace($offset = 0)
    {
        $backTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $trace = $this->cleanTrace($backTrace);

        if (is_integer($offset)) {
            for ($i = 1; $i <= $offset; ++$i) {
                array_shift($trace);
            }
        }

        return $trace;
    }

    /**
     * Runs an get_included_files() and filters result
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
                $files[$key]['file'] = $file;
            }
        }

        array_unshift($files, array('Called Order', 'File'));

        return $files;
    }

    /**
     * Executes a pr() request only if argument in loopCond is true
     * @param $cond boolean
     * @return
     */
    public function loopCond($cond)
    {
        $trace = $this->getDebugTrace();
        $file = md5($trace[0]['file']);
        $line = $trace[0]['line'];
        $index = "$file-$line";

        if (!is_bool($cond)) {
            $cond = (boolean) $cond;
        }
        self::$loopCondStatus[$index]['boolean'] = $cond;

        return $this;
    }

    /**
     * Sets and starts the loop range printing
     * If end is not set, it will print upto the end if the loop.
     * @param integer $start When to start printing inside a loop
     * @param integer $end When to stop printing inside a loop
     */
    public function loopRange($start, $end = null)
    {
        $trace = $this->getDebugTrace();
        $file = md5($trace[0]['file']);
        $line = $trace[0]['line'];
        $index = "$file-$line";

        if (!isset(self::$loopRangeStamp[$index])) {
            self::$loopRangeStamp[$index]['iterator'] = 1;
            self::$loopRangeStamp[$index]['start'] = $start;
            self::$loopRangeStamp[$index]['end'] = $end;
        } else {
            ++self::$loopRangeStamp[$index]['iterator'];
        }

        return $this;
    }

    /**
     * Print PHP's predefined variables
     * @param string $variable
     * @param int $offsetTrace
     */
    public function prPredefinedVars($variable, $offsetTrace = 0)
    {
        if (!$this->isEnvironmentProd()) {
            $variable = strtoupper($variable);
            if ($variable == 'SERVER' || $variable == 'GET' || $variable == 'POST'
                || $variable == 'FILES' || $variable == 'REQUEST' || $variable == 'SESSION'
                    || $variable == 'ENV' || $variable == 'COOKIE'
                        ) {
                $content = $GLOBALS["_$variable"];

                $trace = $this->getDebugTrace($offsetTrace);
                $file = $trace[0]['file'];
                $line = $trace[0]['line'];
                $extra['group'] = "pr() (Printing {$variable})";
            }
        }
    }

    /**
     * Run a debug backtrace
     * @param void
     * @return null
     */
    public function trace()
    {
        if (!$this->isEnvironmentProd()) {
            $trace = $this->getDebugTrace();
            if ($trace[0]['function'] == 'trace') array_shift($trace);
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
            $extra['group'] = 'trace() (Function Tracing)';

            while (list($key) = each($trace[0])) {
                if ($key != 'file' && $key != 'line') {
                    unset($trace[0][$key]);
                }
            }

            $trace[0]['notice'] = "END OF TRACE";

            // use file tracing if we didnt retrieve any result
            if (count($trace) == 1) {
                $trace[0]['notice'] = "(END OF TRACE) FUNCTION TRACE DID NOT RETRIEVE RESULTS. SWITCHED TO FILE TRACING";
                $fileTrace = $this->getFileTrace();
                array_pop($fileTrace);
                array_shift($fileTrace);
                $fileTrace[] = array_pop($trace);
                $trace = array_reverse($fileTrace);
            }
        }
    }

    /**
     * Run a file trace
     * @param void
     * @return null
     */
    public function prFiles()
    {
        $trace = $this->getDebugTrace();
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        $extra['group'] = 'prFiles() (Print Included Files)';

        $fileTrace = $this->getFileTrace();
        array_pop($fileTrace);
        array_shift($fileTrace);
        $fileTrace = array_reverse($fileTrace);

        $last['notice'] = 'END OF TRACE';
        $last['file'] = $file;
        array_unshift($fileTrace, $last);
    }

    /**
     * Trace the instances of the arguments
     * @param string $variable The name of the variable to trace
     * @return null
     */
    public function traceVariable($variable = '')
    {
        if (!is_string($variable)) {
            throw new \ErrorException('trVar() only accepts string.');
        }

        if (!empty($variable)) {
            $trace = $this->getDebugTrace();
            if ($trace[0]['function'] == 'traceVariable') array_shift($trace);
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
            $extra['group'] = 'traceVariable() (Variable Tracing)';

            $output = $this->parseVariableFromXdebug($variable);
        }
    }

    /**
     * Cleans a trace and avoids the include(), require(), etc..
     * @param void
     * @return array
     */
    protected function cleanTrace(array $trace)
    {
        $cleanTrace = array();
        foreach ($trace as $val) {
            if ($val['function'] != 'require'
                && $val['function'] != 'require_once'
                && $val['function'] != 'include'
                && $val['function'] != 'include_once'
            ) {
                if (isset($val['file']) && strpos($val['file'], 'phar://') === 0) {
                    continue;
                }

                $cleanTrace[] = $val;
            }
        }
        array_shift($cleanTrace);
        return $cleanTrace;
    }

    /**
     * Header to clean the trace_var array
     * @param array $contents
     * @param string $var
     * @return array Cleaned content
     */
    protected function cleanTraceVar(array $contents, $var = '')
    {
        $return = array();
        foreach ($contents as $key => $content) {
            $return[$key]["file"] = $content['file'];
            $return[$key]["line"] = $content['line'];

            if (!empty($var)) {
                preg_match_all("~(\\\${$var} = .*?)[;,\s\)\}]~i", $content['initialization'], $matches);
                if (!empty($matches[1])) {
                    $return[$key]["var(\${$var}) assignment"] = $matches[1];
                } else {
                    $return[$key]["var(\${$var}) assignment"] = 'see initialization';
                }
            }

            $initialization = preg_replace(array('~\\\t~', '~\\\n~', '~\\\~'), array(' ', ''), $content['initialization']);
            $initialization = preg_replace('~\s\s+~', ' ', $initialization);

            $return[$key]["initialization"] = $initialization;
        }

        return $return;
    }

    /**
     * Cleans the output of the result array of Xdebug
     * @param array $output
     * @param boolean $showZF Should we output the ZF library in the trace?
     * @return array
     */
    protected function cleanXdebugTrace(array $trace, $showZF = false)
    {
        $cleanedOutput = array();
        foreach ($trace as $key => $val) {
            if (strpos($val, "nav_debug") === false && (strpos($val, 'library/Zend') === false || $showZF == true)) {
                $val = trim($val);
                $lastSpace = (int) strrpos($val, ' ');
                $fileNameWithLine = trim(substr($val, $lastSpace));
                $fileNameArray = explode(":", $fileNameWithLine); // separate the file from linenumber

                if ($fileNameArray[0]) $cleanedOutput[$key]['file'] = $fileNameArray[0];
                if ($fileNameArray[1]) $cleanedOutput[$key]['line'] = $fileNameArray[1];
                $cleanedOutput[$key]['initialization'] = trim(substr($val, 0, $lastSpace), ' -=>.0123456789');
            }
        }
        return $cleanedOutput;
    }
}

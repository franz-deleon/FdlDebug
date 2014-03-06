<?php
namespace FdlDebug;

use FdlDebug\Writer\WriterInterface;

class Debug extends DebugAbstract
{
    /**
     * Alias to printNow
     * @param mixed $content
     */
    public function pr($content)
    {
        return $this->printNow($content);
    }

    /**
     * Print the content now
     * @param mixed $content
     */
    public function printNow($content)
    {
        $this->getWriter()->write($content);
    }

    /**
     * Print PHP's global variables
     * @param string $variable
     * @param int $offsetTrace
     */
    public function printGlobalVar($variable)
    {
        $variable = strtoupper($variable);
        if ($variable == 'SERVER' || $variable == 'GET' || $variable == 'POST'
            || $variable == 'FILES' || $variable == 'REQUEST' || $variable == 'SESSION'
            || $variable == 'ENV' || $variable == 'COOKIE'
        ) {
            $content = $GLOBALS["_$variable"];
            $this->getWriter()->write($content);
        }
    }

    /**
     * Run a debug backtrace
     * @param void
     * @return null
     */
    public function printBackTrace()
    {
        $trace = $this->getBackTrace();
        $trace = $this->findTraceKeyAndSlice($trace, 'function', __FUNCTION__, 3); // 3 to offset the Front class __call
        $trace[0]['notice'] = "END OF TRACE";

        $this->getWriter()->write($trace);
    }

    /**
     * Run a file trace
     * @param void
     * @return null
     */
    public function printFiles()
    {
        $trace = $this->getBackTrace();
        $file  = $trace[0]['file'];
        $line  = $trace[0]['line'];
        $extra['group'] = 'prFiles() (Print Included Files)';

        $fileTrace = $this->getFileTrace();
        array_pop($fileTrace);
        array_shift($fileTrace);
        $fileTrace = array_reverse($fileTrace);

        $last['notice'] = 'END OF TRACE';
        $last['file'] = $file;
        array_unshift($fileTrace, $last);

        $this->getWriter()->write($trace);
    }
}

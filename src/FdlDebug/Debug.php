<?php
namespace FdlDebug;

use FdlDebug\Writer\WriterInterface;

class Debug extends DebugAbstract implements DebugInterface
{
    protected $writer;

    /**
     * @param string $writer
     */
    public function __construct($writer)
    {
        $this->setWriter($writer);
    }

    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Alias to printNow
     * @param mixed $content
     */
    public function pr($content)
    {
        return $this->printNow($content);
    }

    public function printNow($content)
    {
        $this->getWriter()->write($content);
    }

    /**
     * Print PHP's global variables
     * @param string $variable
     * @param int $offsetTrace
     */
    public function printGlobalVars($variable, $offsetTrace = 0)
    {
        $variable = strtoupper($variable);
        if ($variable == 'SERVER' || $variable == 'GET' || $variable == 'POST'
            || $variable == 'FILES' || $variable == 'REQUEST' || $variable == 'SESSION'
            || $variable == 'ENV' || $variable == 'COOKIE'
        ) {
            $content = $GLOBALS["_$variable"];

            $trace = $this->getBackTrace($offsetTrace);
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
            $extra['group'] = "pr() (Printing {$variable})";
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
    }

    /**
     * Run a file trace
     * @param void
     * @return null
     */
    public function printFiles()
    {
        $trace = $this->getBackTrace();
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
     * Trace the instances of a variable
     * @param string $variable The name of the variable to trace
     * @return null
     */
    public function printTracedVariable($variable)
    {
        if (!is_string($variable)) {
            throw new \ErrorException('trVar() only accepts string.');
        }

        if (!empty($variable)) {
            $trace = $this->getBackTrace();
            if ($trace[0]['function'] == 'traceVariable') array_shift($trace);
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
            $extra['group'] = 'traceVariable() (Variable Tracing)';

            $output = $this->parseVariableFromXdebug($variable);
        }
    }
}

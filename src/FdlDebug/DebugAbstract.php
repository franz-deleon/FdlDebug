<?php
namespace FdlDebug;

use FdlDebug\Writer\WriterInterface as Writer;

abstract class DebugAbstract implements DebugInterface
{
    /**
     * @var Writer
     */
    protected $writer;

    /**
     * The file number instance
     * @var string
     */
    protected $file;

    /**
     * The line number instance
     * @var string|int
     */
    protected $line;

    /**
     * The callers method name
     * @var string
     */
    protected $method;

    /**
     * Constructor.
     * Pass the writer
     *
     * @param string $writer
     */
    public function __construct($writer = null)
    {
        if (null !== $writer) {
            $this->setWriter($writer);
        }
    }

    /**
     * Retrieve the writer
     * @return Writer
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\DebugInterface::setWriter()
     */
    public function setWriter(Writer $writer)
    {
        $this->writer = $writer;
        return $this;
    }

    /**
     * The file name from __call
     * @param string $file
     * @return \FdlDebug\Condition\AbstractCondition
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Retrieve the file name
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the line number from __call
     * @param int $line
     * @return \FdlDebug\Condition\AbstractCondition
     */
    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }

    /**
     * Retrieve the line number from __call
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set the method
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Retrieve the method
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Runs a PHP native debug_backtrace
     * @param void
     * @return array
     */
    public function getBackTrace($offset = 0)
    {
        $backTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $this->cleanTrace($backTrace);

        if (is_integer($offset) && $offset !== 0) {
            for ($i = 1; $i <= $offset; ++$i) {
                array_shift($backTrace);
            }
        }

        return $backTrace;
    }

    /**
     * Runs a get_included_files() and filters result
     * @param  boolean $showVendor
     * @return array
     */
    public function getFileTrace($showVendor = false)
    {
        $files = array();
        foreach (get_included_files() as $key => $file) {
            if (stripos($file, 'phar:') !== false
                || (stripos($file, '/vendor/') !== false && $showVendor == false)
                || (stripos($file, '/' . basename(realpath(__DIR__ . '/../../')) . '/') !== false)
            ) {
                if (! (stripos($file, '/tests/FdlDebugTests/') !== false)) {
                    continue;
                }
            }

            $files[$key]['order'] = ($key + 1);
            $files[$key]['file']  = $file;
        }

        array_unshift($files, array('Called Order', 'File'));

        return $files;
    }

    /**
     * Slices a backtrace array result
     *
     * @param array  $traceArray              The target trace array from getBackTrace()
     * @param string $traceKey                The target backtrace key
     * @param string $traceValueToSearch      The target backtrace's key value
     * @param int    $fromSearchedValueOffset The offset that starts from a successfully searched $traceValueToSearch
     * @param int    $fromStartOffset         The offset that starts from the beginning of $traceArray
     * @param bool   $startFromEnd            Should the array search start from the end?
     * @return array
     */
    public function findTraceKeyAndSlice(
        $traceArray,
        $traceKey,
        $traceValueToSearch,
        $fromSearchedValueOffset = 0,
        $fromStartOffset = 0,
        $startFromEnd = false
    ) {
        if (is_string($traceValueToSearch)) {
            $traceValueToSearch = array($traceValueToSearch);
        }
        if ($startFromEnd == true) {
            $traceArray = array_reverse($traceArray);
        }
        if ($fromStartOffset > 0) {
            $traceArray = array_slice($traceArray, $fromStartOffset);
        }

        foreach ($traceArray as $key => $val) {
            if (in_array($val[$traceKey], $traceValueToSearch)) {
                if ($startFromEnd == true) {
                    $traceArray = array_slice($traceArray, 0, ($key + 1) + -abs($fromSearchedValueOffset));
                    break;
                } else {
                    return array_slice($traceArray, ($key + abs($fromSearchedValueOffset)));
                }
            }
        }

        if ($startFromEnd == true) {
            return array_reverse($traceArray);
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
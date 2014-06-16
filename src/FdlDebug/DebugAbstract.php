<?php
namespace FdlDebug;

use FdlDebug\Writer\WriterInterface;

abstract class DebugAbstract implements DebugInterface
{
    protected $writer;

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
     * Return the writer
     * @return WriterInterface
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\DebugInterface::setWriter()
     */
    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
        return $this;
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
        $fdlDebugDirname = basename(realpath(__DIR__ . '/../../'));
        foreach (get_included_files() as $key => $file) {
            if (stripos($file, 'phar:') !== false
                || (stripos($file, '/vendor/') !== false && $showVendor == false)
                || (stripos($file, "/{$fdlDebugDirname}/") !== false)
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
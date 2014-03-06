<?php
namespace FdlDebug\Extension;

use FdlDebug\StdLib;
use FdlDebug\DebugInterface;
use FdlDebug\Writer\WriterInterface;

class Xdebug implements DebugInterface
{
    protected $writer;

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\DebugInterface::setWriter()
     */
    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Trace the instances of a variable
     * @param string $variable The name of the variable to trace
     * @return null
     */
    public function printXdebugTracedVar($variable)
    {
        if (StdLib\Utility::isXDebugEnabled()) {
            if (!is_string($variable)) {
                throw new \ErrorException('printTracedVariable() only accepts string.');
            }

            if (!empty($variable)) {
                $output = $this->xdebugParseVariable($variable);
                $this->writer->write($output);
            }

            xdebug_stop_trace();
        } else {
            throw new \ErrorException('Xdebug is disabled');
        }
    }

    /**
     * Cleans the output of the result array of Xdebug
     * @param array $output
     * @param boolean $showZF Should we output the ZF library in the trace?
     * @return array
     */
    protected function xdebugCleanTrace(array &$trace, $showVendor = false)
    {
        $cleanedOutput = array();
        foreach ($trace as $key => $val) {
            if (strpos($val, "fdldebug") === false
                && (strpos($val, 'vendor') === false || $showVendor == true)
            ) {
                $val              = trim($val);
                $lastSpace        = (int) strrpos($val, ' ');
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
     * Header to clean the trace_var array
     * @param array $contents
     * @param string $var
     */
    protected function xdebugFormatTrace(array &$contents, $var = '')
    {
        $newContent = array();
        foreach ($contents as $key => $content) {
            $newContent[$key]["file"] = $content['file'];
            $newContent[$key]["line"] = $content['line'];

            if (!empty($var)) {
                preg_match_all("~(\\\${$var} = .*?)[;,\s\)\}]~i", $content['initialization'], $matches);
                if (!empty($matches[1])) {
                    $newContent[$key]["var(\${$var}) assignment"] = $matches[1];
                } else {
                    $newContent[$key]["var(\${$var}) assignment"] = 'see initialization ▼';
                }
            }

            $content['initialization'] = preg_replace(array('~\\\t~', '~\\\n~', '~\\\~'), array(' ', ''), $content['initialization']);
            $content['initialization'] = preg_replace('~\s\s+~', ' ', $content['initialization']);

            $newContent[$key]["initialization"] = $content['initialization'];
        }

        $contents = $newContent;
    }

    /**
     * Parse the search string from the xdebug trace log
     * @param string $var
     * @return array
     */
    public function xdebugParseVariable($var)
    {
        $var = trim($var, '$ ');

        $traceFile = StdLib\Utility::getXdebugTraceFile();
        $exec = "grep -i \"\\\$*>{$var}\\b\" "  . $traceFile;
        exec($exec, $output);
        $exec = "grep -i \"\\\${$var}\\b\" " . $traceFile;
        exec($exec, $output);

        $this->xdebugCleanTrace($output);
        $this->xdebugFormatTrace($output, $var);

        return $output;
    }
}

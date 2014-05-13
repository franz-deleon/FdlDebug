<?php
namespace FdlDebug;

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
        return $this->getWriter()->write($content);
    }

    /**
     * Print and die
     * @param mixed $content
     */
    public function prDie($content)
    {
        $r = $this->printNow($content);
        if ($r !== null) {
            echo $r;
        }
        die;
    }

    /**
     * Print PHP's global variables
     * @param string $type
     * @param int $offsetTrace
     */
    public function printGlobal($type = null)
    {
        $globalsList = array('SERVER', 'GET', 'POST', 'FILES', 'REQUEST', 'SESSION', 'ENV', 'COOKIE');

        if (null !== $type) {
            $type = strtoupper($type);
            if (in_array($type, $globalsList)) {
                if (isset($GLOBALS["_$type"])) {
                    return $this->getWriter()->write($GLOBALS["_$type"]);
                }
            }
        } else {
            $return = array();
            foreach ($globalsList as $globalType) {
                if (isset($GLOBALS["_$globalType"])) {
                    $return[$globalType] = $GLOBALS["_$globalType"];
                }
            }
            return $this->getWriter()->write($return);
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

        return $this->getWriter()->write(array_reverse($trace));
    }

    /**
     * Run a file trace
     * @param $showVendor
     * @return null
     */
    public function printFiles($showVendor = false)
    {
        $fileTrace = $this->getFileTrace($showVendor);
        array_shift($fileTrace);

        $last['notice'] = 'END OF TRACE';
        $fileTrace[] = $last;

        return $this->getWriter()->write($fileTrace);
    }
}

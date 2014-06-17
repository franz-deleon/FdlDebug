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
        if (is_object($content)) {
            return $this->printObject($content);
        } else {
            return $this->getWriter()->write($content);
        }
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
     * Defines an object
     */
    public function printObject($object)
    {
        if (is_object($object)) {
            $return = array();
            $return['name'] = $className = get_class($object);
            $return['hash_id']    = spl_object_hash($object);
            $return['methods']    = get_class_methods($className);
            $return['properties'] = get_class_vars($className);
            return $this->pr($return);
        } else {
            return $this->pr('Is not an object');
        }
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

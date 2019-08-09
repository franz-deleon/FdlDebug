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
     * prints the content to its appropriate resource
     * @param mixed     $content Mixed resource content
     * @return mixed
     */
    public function printNow($content)
    {
        $content = $this->resourceTypeFactory($content);
        return $this->getWriter()->write($content, array('function' => __FUNCTION__));
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
                    return $this->getWriter()->write($GLOBALS["_$type"], array('function' => __FUNCTION__, 'type' => $type));
                }
            }
        } else {
            $return = array();
            foreach ($globalsList as $globalType) {
                if (isset($GLOBALS["_$globalType"])) {
                    $return[$globalType] = $GLOBALS["_$globalType"];
                }
            }
            return $this->getWriter()->write($return, array('function' => __FUNCTION__));
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
        $trace = $this->findTraceKeyAndSlice($trace, 'function', array(__FUNCTION__, 'pr_backtrace'), 0, 0, true);
        $trace[0]['notice'] = "END OF BACKTRACE";

        return $this->getWriter()->write(array_reverse($trace), array('function' => __FUNCTION__));
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

        $last = array_pop($fileTrace);
        $lastOrder = (int) $last['order'];

        $fileTrace[] = $last; //readd the popped element
        $fileTrace[] = array('order' => ++$lastOrder, 'file' => 'END');

        return $this->getWriter()->write($fileTrace, array('function' => __FUNCTION__));
    }

    /**
     * Print the content now
     * @param mixed $content
     */
    protected function resourceTypeFactory($content)
    {
        if (is_object($content)) {
            $content = $this->parseObject($content);
        } elseif (is_array($content)) {
            $content = $this->parseArray($content);
        } elseif (is_bool($content)) {
            $content = $this->parseBool($content);
        } elseif ($content === null) {
            $content = $this->parseNull($content);
        }

        return $content;
    }

    /**
     * Parse an array resource
     * @param array $content
     * @return array
     */
    protected function parseArray(array $content)
    {
        foreach ($content as &$v) {
            $v = $this->resourceTypeFactory($v);
        }
        unset($v);

        return $content;
    }

    /**
     * Parse a boolean resource
     * @param bool $content
     * @return string
     */
    protected function parseBool($content)
    {
        if (is_bool($content)) {
            return 'boolean: ' . (($content === true) ? 'true' : 'false');
        }
        return '';
    }

    /**
     * Parse a null value
     * @param null $content
     * @return string
     */
    protected function parseNull($content)
    {
        if ($content === null) {
            return 'NULL';
        }
        return '';
    }

    /**
     * Defines an object
     */
    protected function parseObject($object, $showRaw = false)
    {
        if (is_object($object)) {
            $return = array();
            $return['name'] = $className = get_class($object);
            $return['hash_id']    = spl_object_hash($object);
            $return['methods']    = get_class_methods($className);
            $return['properties'] = get_class_vars($className);
            if ($showRaw) $return['raw'] = $object;

            sort($return['methods']);
            asort($return['properties']);

            return $return;
        }
        return '';
    }
}

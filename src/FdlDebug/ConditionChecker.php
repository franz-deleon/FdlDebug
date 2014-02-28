<?php
namespace FdlDebug;

class ConditionChecker extends Debug
{
    /**
     * Holds the loop range instances
     * @var array
     */
    protected static $loopRangeStamp = array();

    /**
     * Holds the loop condition status
     * @var boolean
     */
    protected static $loopCondStatus = array();

    /**
     * Did the checks passed?
     * @param string $file
     * @param string $line
     */
    public function isPassed($file, $line)
    {
        // checks
        $loopRange = $this->checkLoopRange($file, $line);
        $loopCond  = $this->checkLoopCond($file, $line);

        if ($loopRange !== null || $loopCond !== null) {
            $index = md5($file) . '-' . $line;
            if (!empty(self::$loopRangeStamp[$index]) && !empty(self::$loopCondStatus[$index])) {
                if ($loopRange === true && $loopCond === true) {
                    return true;
                }
            } else {
                if ($loopRange === true || $loopCond === true) {
                    return true;
                }
            }
        } else {
            return true;
        }
        return false;
    }

    /**
     * Executes a pr() request only if argument in loopCond is true
     * @param $condition boolean
     * @return
     */
    public function setLoopCond($condition)
    {
        $trace = $this->getBackTrace();
        $index = md5($trace[0]['file']) . '-' . $trace[0]['line'];

        if (!is_bool($condition)) {
            $condition = (boolean) $condition;
        }
        self::$loopCondStatus[$index]['boolean'] = $condition;

        return $this;
    }

    /**
     * Sets and starts the loop range printing
     * If end is not set, it will print upto the end of the loop.
     * @param integer $start When to start printing inside a loop
     * @param integer $end When to stop printing inside a loop
     */
    public function setLoopRange($start, $end = null)
    {
        $trace = $this->getBackTrace();
        $index = md5($trace[0]['file']) . '-' . $trace[0]['line'];

        if (!isset(self::$loopRangeStamp[$index])) {
            self::$loopRangeStamp[$index]['iterator'] = 1;
            self::$loopRangeStamp[$index]['start']    = $start;
            self::$loopRangeStamp[$index]['end']      = $end;
        } else {
            ++self::$loopRangeStamp[$index]['iterator'];
        }

        return $this;
    }

    /**
     * Checks the loop range whether to start printing or not.
     * @param integer $file
     * @param integer $line
     * @return boolean|null
     */
    private function checkLoopRange($file, $line)
    {
        $file  = md5($file);
        $index = "$file-$line";
        if (!empty(self::$loopRangeStamp[$index])) {
            if (self::$loopRangeStamp[$index]['iterator'] >= self::$loopRangeStamp[$index]['start']) {
                if (isset(self::$loopRangeStamp[$index]['end'])) {
                    if (self::$loopRangeStamp[$index]['iterator'] > self::$loopRangeStamp[$index]['end']) {
                        return false;
                    }
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Is the loop condition true?
     * @param integer $file
     * @param integer $line
     * @return boolean|null
     */
    private function checkLoopCond($file, $line)
    {
        $file = md5($file);
        $index = "$file-$line";
        if (!empty(self::$loopCondStatus[$index])) {
            if (self::$loopCondStatus[$index]['boolean'] === true) {
                return true;
            } elseif (self::$loopCondStatus[$index]['boolean'] === false) {
                return false;
            }
        }
    }
}

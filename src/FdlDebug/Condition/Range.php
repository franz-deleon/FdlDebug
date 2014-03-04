<?php
namespace FdlDebug\Condition;

class Range extends AbstractCondition implements ConditionsInterface
{
    /**
     * Holds the loop range instances
     * @var array
     */
    protected static $loopRangeStamp = array();

    /**
     * Implementation of getMethod function
     * @param integer $start
     * @param integer $end
     * @return \FdlDebug\Condition\Range
     */
    public function loopRange($start, $end = null)
    {
        $index = $this->getCreatedIndex();
        if (empty(self::$loopRangeStamp[$index])) {
            self::$loopRangeStamp[$index]['iterator'] = 1;
            self::$loopRangeStamp[$index]['start']    = $start;
            self::$loopRangeStamp[$index]['end']      = $end;
        } else {
            ++self::$loopRangeStamp[$index]['iterator'];
        }

        return $this;
    }

    public function evaluationCallbackMethod()
    {
        return 'loopRange';
    }

    public function evaluate()
    {
        $index = $this->getCreatedIndex();
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

    public function useDebugTracingForIndex()
    {
        return true;
    }
}
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

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::evaluationCallbackMethod()
     */
    public function evaluationCallbackMethod()
    {
        return 'loopRange';
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::evaluate()
     */
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

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\AbstractCondition::useDebugTracingForIndex()
     */
    public function useDebugTracingForIndex()
    {
        return true;
    }

    /**
     * Do nothing
     * @see \FdlDebug\Condition\ConditionsInterface::postDebug()
     */
    public function postDebug()
    {
        return;
    }
}
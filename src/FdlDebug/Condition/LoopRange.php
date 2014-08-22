<?php
namespace FdlDebug\Condition;

use FdlDebug\Front;

class LoopRange extends AbstractCondition implements ConditionsInterface
{
    /**
     * Holds the loop range instances
     * @var array
     */
    protected $loopRangeStamp = array();

    /**
     * Nested loop range
     * @var array
     */
    protected $nestedLoopCounter = array();

    /**
     * Implementation of loop range logic
     * @param integer $start  Where to start outputting the loop
     * @param integer $length Where to end starting from start
     * @return \FdlDebug\Condition\Range
     */
    public function loopRange($start = 1, $length = null)
    {
        $index = $this->getUniquePosition();
        if (empty($this->loopRangeStamp[$index])) {
            $this->loopRangeStamp[$index]['iterator'] = 1;
            $this->loopRangeStamp[$index]['start']    = $start;
            $this->loopRangeStamp[$index]['length']   = $length;
        } else {
            ++$this->loopRangeStamp[$index]['iterator'];
        }

        $this->nestedLoopCounter[$index] = $index;

        return $this;
    }

    /**
     * An accessible function to identify an end of a loop
     * @param void
     * @return null;
     */
    public function rangeNestedEnd()
    {
        $lastIndex = array_pop($this->nestedLoopCounter);
        if ($this->loopRangeStamp[$lastIndex]['iterator']) {
            $this->loopRangeStamp[$lastIndex]['iterator'] = 0;
        }
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
     * @see \FdlDebug\Condition\AbstractCondition::unevaluatedCallbackMethods()
     * @overload
     */
    public function unevaluatedCallbackMethods()
    {
        return array('rangeNestedEnd');
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::evaluate()
     */
    public function evaluate()
    {
        $index = $this->getUniquePosition();
        if (!empty($this->loopRangeStamp[$index])) {
            $iterator = $this->loopRangeStamp[$index]['iterator'];
            $start    = $this->loopRangeStamp[$index]['start'];
            $length   = $this->loopRangeStamp[$index]['length'];

            if ($iterator >= $start) {
                if (null !== $length) {
                    $offsetLength = ($start + $length) - 1;
                    if ($iterator > $offsetLength) {
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
     * @see \FdlDebug\Condition\AbstractCondition::enableDebugTracing()
     */
    public function enableDebugTracing()
    {
        return true;
    }

    /**
     * Do nothing
     * @see \FdlDebug\Condition\ConditionsInterface::preDebug()
     */
    public function preDebug()
    {
    }

    /**
     * Do nothing
     * @see \FdlDebug\Condition\ConditionsInterface::postDebug()
     */
    public function postDebug($return = null, $passed)
    {
    }
}
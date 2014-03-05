<?php
namespace FdlDebug\Condition;

class LoopRange extends AbstractCondition implements ConditionsInterface
{
    /**
     * Holds the loop range instances
     * @var array
     */
    protected $loopRangeStamp = array();

    /**
     * Implementation of loop range logic
     * @param integer $start  Where to start outputting the loop
     * @param integer $length Where to end starting from start
     * @return \FdlDebug\Condition\Range
     */
    public function loopRange($start, $length = null)
    {
        $index = $this->getUniqueIndex();
        if (empty($this->loopRangeStamp[$index])) {
            $this->loopRangeStamp[$index]['iterator'] = 1;
            $this->loopRangeStamp[$index]['start']    = $start;
            $this->loopRangeStamp[$index]['length']   = $length;
        } else {
            ++$this->loopRangeStamp[$index]['iterator'];
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
        $index = $this->getUniqueIndex();
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
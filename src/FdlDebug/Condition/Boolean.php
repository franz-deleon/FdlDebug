<?php
namespace FdlDebug\Condition;

class Boolean implements ConditionsInterface
{
    /**
     * Holds the loop condition status
     * @var boolean
     */
    protected $condition;

    /**
     * Implementation for getMethod
     *
     * @see Boolean::getMethod()
     * @param mixed $condition
     * @return \FdlDebug\Condition\Boolean
     */
    public function condBoolean($condition)
    {
        if (!is_bool($condition)) {
            $this->condition = (boolean) $condition;
        } else {
            $this->condition = $condition;
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::evaluationCallbackMethod()
     */
    public function evaluationCallbackMethod()
    {
        return 'condBoolean';
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::evaluate()
     */
    public function evaluate()
    {
        if (is_bool($this->condition)) {
            return $this->condition;
        }
    }

    /**
     * Do nothing
     * @see \FdlDebug\Condition\ConditionsInterface::postDebug()
     */
    public function postDebug($callbackReturnVal, $passed)
    {
    }

    /**
     * Do nothing
     * @see \FdlDebug\Condition\ConditionsInterface::preDebug()
     */
    public function preDebug()
    {
    }
}

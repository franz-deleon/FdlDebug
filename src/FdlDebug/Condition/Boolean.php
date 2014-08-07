<?php
namespace FdlDebug\Condition;

class Boolean extends AbstractCondition implements ConditionsInterface
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

    public function evaluationCallbackMethod()
    {
        return 'condBoolean';
    }

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
    public function postDebug()
    {
        return;
    }

    /**
     * Do nothing
     * @see \FdlDebug\Condition\ConditionsInterface::preDebug()
     */
    public function preDebug()
    {
    }
}

<?php
namespace FdlDebug\Condition;

class Boolean extends AbstractCondition implements ConditionsInterface
{
    /**
     * Holds the loop condition status
     * @var boolean
     */
    protected static $conditionStack = array();
    protected $condition;

    /**
     * Implementation for getMethod
     *
     * @see Boolean::getMethod()
     * @param mixed $condition
     * @return \FdlDebug\Condition\Boolean
     */
    public function setCondBoolean($condition)
    {
        if (!is_bool($condition)) {
            $this->condition = (boolean) $condition;
        }
        $this->condition = $condition;

        //self::$conditionStack[$this->getDebugInstance()]['boolean'] = $condition;

        return $this;
    }

    public function getMethod()
    {
        return 'setCondBoolean';
    }

    public function evaluate()
    {
        if (is_bool($this->condition)) {
            return $this->condition;
        }
    }
}

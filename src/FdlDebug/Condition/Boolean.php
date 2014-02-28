<?php
namespace FdlDebug\Condition;

class Boolean extends AbstractCondition implements ConditionsInterface
{
    /**
     * Holds the loop condition status
     * @var boolean
     */
    protected static $conditionStack = array();

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
            $condition = (boolean) $condition;
        }

        self::$conditionStack[$this->getDebugInstance()]['boolean'] = $condition;

        return $this;
    }

    public function getMethod()
    {
        return 'setCondBoolean';
    }

    public function check()
    {
        $index = $this->getCreatedIndex();
        if (null !== $index) {
            if (!empty(self::$conditionStack[$index])) {
                if (self::$conditionStack[$index]['boolean'] === true) {
                    return true;
                }
                return false;
            }
        }
    }
}

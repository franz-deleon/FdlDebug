<?php
namespace FdlDebug\Condition;

class ConditionsManager
{
    /**
     * Conditions stack
     * @var array Array collection of ConditionsInterface
     */
    protected $conditions = array();

    /**
     * A mapper array with key as the method
     * name and val as condition class name
     * @var array
     */
    protected $conditionsMethodName = array();

    /**
     * Constructor
     */
    public function __construct(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (class_exists($condition)) {
                $this->addConditions(new $conditions);
            } elseif (class_exists($condName = __NAMESPACE__ . '\\' . ucfirst($condition))) {
                $this->addConditions(new $condName);
            }
        }
    }

    /**
     * Add a new condition checker to the stack
     *
     * @param ConditionsInterface $condition
     * @throws \ErrorException
     */
    public function addConditions(ConditionsInterface $condition)
    {
        $methodName = $condition->getMethod();
        if (isset($this->conditionsMethodName[$methodName])) {
            throw new \ErrorException(sprintf(
                "Method %s already exist in another condition",
                $methodName
            ));
        }

        $className = get_class($condition);
        $this->conditions[$className] = $condition;
        $this->conditionsMethodName[$methodName] = $className;
    }

    /**
     * Checks a condition from the stack.
     *
     * @param string $methodName
     * @return boolean|null
     */
    public function methodCheck($methodName)
    {
        if (isset($this->conditions[$methodName])) {
            return $this->conditions[$methodName]->check();
        }
    }

    /**
     * Return a condition object by method name
     * @param  string $methodName
     * @return ConditionsInterface|null
     */
    public function getConditionByMethodName($methodName)
    {
        if ($this->doesConditionMethodExists($methodName)) {
            return $this->conditions[$this->conditionsMethodName[$methodName]];
        }
    }

    /**
     * Does the condition method name exists?
     * @param string $methodName
     */
    public function doesConditionMethodExists($methodName)
    {
        if (!empty($this->conditionsMethodName[$methodName])) {
            return true;
        }
        return false;
    }
}

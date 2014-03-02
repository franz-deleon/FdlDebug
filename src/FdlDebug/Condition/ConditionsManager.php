<?php
namespace FdlDebug\Condition;

use FdlDebug\StdLib\Utility;

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
     * An array of conditional expressions that converted
     * to boolean values using operands and operators
     * @var array
     */
    protected $conditionsExpression = array();

    /**
     * Constructor.
     * Initialized the conditions
     * @param array $conditions
     */
    public function __construct(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (class_exists($condition)) {
                $this->addConditions(new $condition);
            } elseif (class_exists($condition = __NAMESPACE__ . '\\' . Utility::underscoreToCamelcase($condition))) {
                $this->addConditions(new $condition);
            }
        }
    }

    /**
     * Add a new condition checker to the stack
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
     * Did the conditions passed for this instance?
     * Evaluates the conditions expressions.
     *
     * @param void
     * @return boolean
     */
    public function isPassingEvaluation($instanceId)
    {
        if (!empty($this->conditionsExpression[$instanceId]['operand'])) {
            $evalStatement = '';

            $last = count($this->conditionsExpression[$instanceId]['operand']) - 1;
            foreach ($this->conditionsExpression[$instanceId]['operand'] as $current => $operand) {
                if ($current < $last) {
                    $evalStatement .= $operand . ' ' . $this->conditionsExpression[$instanceId]['operator'][$current] . ' ';
                } else {
                    $evalStatement .= $operand;
                }
            }

            return (bool) eval("return $evalStatement;");
        }
        return true;
    }


    public function addConditionsOperand($instanceId, $operand)
    {
        $this->conditionsExpression[$instanceId]['operand'][] = (int) $operand;
        return $this;
    }

    public function addConditionsOperator($instanceId, $operator)
    {
        $this->conditionsExpression[$instanceId]['operator'][] = $operator;
        return $this;
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

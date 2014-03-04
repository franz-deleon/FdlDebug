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
     * Called conditions
     * @var array Array of Condition\ConditionsInterface
     */
    protected $calledConditions = array();

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
        $methodName = $condition->evaluationCallbackMethod();
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
     * Return the conditions list or a specific condition
     * @param string $className
     * @return ConditionsInterface
     */
    public function getConditions($className = null)
    {
        if (null !== $className && isset($this->conditions[$className])) {
            return $this->conditions[$className];
        }
        return $this->conditions;
    }

    /**
     * Evaluates the conditions expressions.
     *
     * @param void
     * @return boolean
     */
    public function evaluateExpressions($instanceId)
    {
        if (!empty($this->conditionsExpression[$instanceId]['operand'])) {
            $evalStatement = '';

            $lastKey = Utility::arrayLastKey($this->conditionsExpression[$instanceId]['operand']);
            foreach ($this->conditionsExpression[$instanceId]['operand'] as $key => $operand) {
                if ($key < $lastKey) {
                    $evalStatement .= $operand . ' ' . $this->conditionsExpression[$instanceId]['operator'][$key] . ' ';
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

    public function getConditionsOperand($instanceId)
    {
        if (isset($this->conditionsExpression[$instanceId]['operand'])) {
            return $this->conditionsExpression[$instanceId]['operand'];
        }
    }

    public function setConditionsOperand($instanceId, array $operand)
    {
        $this->conditionsExpression[$instanceId]['operand'] = $operand;
        return $this;
    }

    public function addConditionsOperator($instanceId, $operator)
    {
        $this->conditionsExpression[$instanceId]['operator'][] = $operator;
        return $this;
    }

    public function getConditionsOperator($instanceId)
    {
        if (isset($this->conditionsExpression[$instanceId]['operator'])) {
            return $this->conditionsExpression[$instanceId]['operator'];
        }
    }

    public function setConditionsOperator($instanceId, array $operator)
    {
        $this->conditionsExpression[$instanceId]['operator'] = $operator;
        return $this;
    }

    /**
     * Return a condition object by method name
     * @param  string $methodName
     * @return ConditionsInterface|null
     */
    public function getConditionByMethodName($methodName)
    {
        if ($this->isExistingConditionsMethod($methodName)) {
            return $this->conditions[$this->conditionsMethodName[$methodName]];
        }
    }

    public function getCalledConditions()
    {
        return $this->calledConditions;
    }

    public function addCalledConditions(ConditionsInterface $condition)
    {
        $this->calledConditions[] = $condition;
    }

    public function setCalledConditions(array $conditions)
    {
        $this->calledConditions = $conditions;
    }

    /**
     * Does the condition method name exists?
     * @param string $methodName
     */
    public function isExistingConditionsMethod($methodName)
    {
        if (!empty($this->conditionsMethodName[$methodName])) {
            return true;
        }
        return false;
    }
}

<?php
namespace FdlDebug\Condition;

use FdlDebug\StdLib\Utility;
use FdlDebug\DebugInterface;

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
     * Called conditions that are cannot be changed
     * @var array Array of Condition\ConditionsInterface
     */
    protected $immutableCalledConditions = array();

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
     * Method names that will not be evaulated or treated as a condition
     * @var array
     */
    protected $unevaluatedCallbackMethods = array();

    /**
     * Constructor.
     * Initialized the conditions
     * @param array  $conditions
     * @param Writer $writer
     */
    public function __construct(array $conditions, $writer = null)
    {
        foreach ($conditions as $condition) {
            if (class_exists($condition)) {
                $condition = new $condition();
            } elseif (class_exists($condition = __NAMESPACE__ . '\\' . Utility::underscoreToCamelcase($condition))) {
                $condition = new $condition();
            }

            $this->registerCondition($condition, $writer);
        }
    }

    /**
     * Register a new condition
     * @param ConditionsInterface $condition
     * @throws \ErrorException
     */
    public function registerCondition(ConditionsInterface $condition, $writer = null)
    {
        if (null !== $writer && $condition instanceof DebugInterface) {
            $condition->setWriter($writer);
        }

        $methodNames = $condition->evaluationCallbackMethod();
        $className   = get_class($condition);

        if (!is_array($methodNames)) {
            $methodNames = array($methodNames);
        }

        if ($condition instanceof AbstractCondition) {
            // inject the conditions manager
            $condition->setConditionsManager($this);

            // register the unevaluated callback methods if there are any
            $unevaluatedMethodnames = $condition->unevaluatedCallbackMethods();
            if (!empty($unevaluatedMethodnames)) {
                if (!is_array($unevaluatedMethodnames)) {
                    $unevaluatedMethodnames = array($unevaluatedMethodnames);
                }

                $methodNames = array_merge($methodNames, $unevaluatedMethodnames);
                $this->unevaluatedCallbackMethods = array_merge($this->unevaluatedCallbackMethods, $unevaluatedMethodnames);
            }
        }

        // assign the condition to the classname
        $this->conditions[$className] = $condition;

        foreach ($methodNames as $methodName) {
            if (isset($this->conditionsMethodName[$methodName])) {
                throw new \ErrorException(sprintf(
                    "Method %s already exist in another condition",
                    $methodName
                ));
            }

            $this->conditionsMethodName[$methodName] = $className;
        }
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

    /**
     * Retrieve the called conditions
     * @return array
     */
    public function getCalledConditions()
    {
        return $this->calledConditions;
    }

    /**
     * Immutable set of called conditions
     * @param ConditionsInterface $condition
     */
    public function addCalledConditions(ConditionsInterface $condition)
    {
        $objHash = spl_object_hash($condition);
        $this->calledConditions[$objHash] = $condition;

        // store every condition to the immutable array
        $this->immutableCalledConditions[$objHash] = $condition;
    }

    /**
     * Reset the called conditions
     * @param array $conditions
     */
    public function resetCalledConditions()
    {
        $this->calledConditions = array();
    }

    /**
     * Retreive the immutable called conditions
     * @return array
     */
    public function getImmutableCalledConditions()
    {
        return $this->immutableCalledConditions;
    }

    /**
     * @return array
     */
    public function getUnevaluatedCallbackMethods()
    {
        return $this->unevaluatedCallbackMethods;
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

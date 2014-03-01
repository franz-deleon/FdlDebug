<?php
namespace FdlDebug;

use FdlDebug\Condition\ConditionsManager;
use FdlDebug\Condition\AbstractCondition;
use ReflectionMethod;

/**
 * This is the entry point for the debug using a front
 * controller like pattern
 */
class Front
{
    /**
     * @var FdlDebug\Front
     */
    protected static $instance;

    /**
     * The unique instance of the debug session
     * @var string
     */
    protected static $debugInstance;

    /**
     * @var ConditionsManager
     */
    protected $conditionsManager;

    /**
     * @var Debug
     */
    protected $debug;

    /**
     * Implementation of singleton pattern
     */
    protected function __construct()
    {
        include __DIR__ . '/../../Bootstrapper.php';

        $configs = Bootstrap::getConfigs();

        $this->debug = new Debug();
        $this->conditionsManager = new ConditionsManager($configs['conditions']);
    }

    /**
     * This is where the magic happens
     *
     * @param string $methodName
     * @param array  $args
     * @return \FdlDebug\Front|mixed
     */
    public function __call($methodName, $args)
    {
        $condition = $this->conditionsManager->getConditionByMethodName($methodName);
        if (null !== $condition) {
            $trace = $this->debug->findTraceKeyAndSlice($this->debug->getBackTrace(), 'function', '__call');
            self::initDebugInstance();
            if ($condition instanceof AbstractCondition) {
                $condition->setDebugInstance(self::$debugInstance);
                $condition->setFile($trace[0]['file'])->setLine($trace[0]['line']);
            }

            // initialize the condition
            call_user_func_array(array($condition, $methodName), $args);

            $this->conditionsManager->addConditionsOperand(self::$debugInstance, $condition->evaluate());
            $this->conditionsManager->addConditionsOperator(self::$debugInstance, '&&');

            // explicitly return '$this' to enable chaining
            return $this;
        }

        if (is_callable(array($this->debug, $methodName))) {
            $pass = $this->conditionsManager->isPassed(self::$debugInstance);

            // Reset the debug instance if and only if the method
            // is an instance of FdlDebug\Debug and not of child classes
            // that extends it.
            $ref = new ReflectionMethod($this->debug, $methodName);
            if ($ref->getDeclaringClass()->getName() === get_class($this->debug)) {
                self::$debugInstance = null;
            }

            if (true === $pass) {
                return call_user_func_array(array($this->debug, $methodName), $args);
            }
        }
    }

    /**
     * Retrieve the instance of this Front class
     */
    public static function i()
    {
        self::initDebugInstance();

        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize a debug instance if it does not exist
     * @param void
     * @return null
     */
    public static function initDebugInstance()
    {
        if (null === self::$debugInstance) {
            self::$debugInstance = uniqid();
        }
    }
}

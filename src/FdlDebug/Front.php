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

    public function __call($methodName, $args)
    {
        $condition = $this->conditionsManager->getConditionByMethodName($methodName);
        if (null !== $condition) {
            $trace = $this->debug->findTraceKeyAndSlice($this->debug->getBackTrace(), 'function', '__call');
            if ($condition instanceof AbstractCondition) {
                self::initDebugInstance();

                $condition->setDebugInstance(self::$debugInstance);
                $condition->setFile($trace[0]['file'])->setLine($trace[0]['line']);
            }

            call_user_func_array(array($condition, $methodName), $args);

            return $this;
        }

        if (is_callable(array($this->debug, $methodName))) {
            // Reset the debug instance if and only if the method
            // is an instance of FdlDebug\Debug and not of its abstract class
            $reflectionMethod = new ReflectionMethod($this->debug, $methodName);
            if ($reflectionMethod->getDeclaringClass()->getName() === get_class($this->debug)) {
                self::$debugInstance = null;
            }

            return call_user_func_array(array($debug, $methodName), $args);
        }
    }

    /**
     * Retrieve the instance of the Front class
     */
    public static function i()
    {
        self::initDebugInstance();

        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function initDebugInstance()
    {
        if (null === self::$debugInstance) {
            self::$debugInstance = uniqid();
        }
    }
}

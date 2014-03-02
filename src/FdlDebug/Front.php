<?php
namespace FdlDebug;

use FdlDebug\Condition\ConditionsManager;
use FdlDebug\Condition\AbstractCondition;
use FdlDebug\StdLib\Utility;

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
     * Container for debug extensions
     * @var array Array containing objects
     */
    protected $debugExtensions = array();

    /**
     * Protected constructor for singleton pattern
     */
    protected function __construct($writer = null)
    {
        include __DIR__ . '/../../Bootstrapper.php';

        $configs = Bootstrap::getConfigs();

        $this->debug             = new Debug($this->initWriter($writer ?: $configs['writer']));
        $this->conditionsManager = new ConditionsManager($configs['conditions']);
        $this->registerExtensions($configs['debug_extensions']);
    }

    /**
     * Retrieve the instance of this Front class
     * @param string $writer An optional writer to pass to override the default writer.
     *                       Note that passing a writer breaks the singleton's
     *                       only one instance and may impact performance as the Front
     *                       object is initialized on every call.
     * @return \FdlDebug\Front
     */
    public static function i($writer = null)
    {
        self::initDebugInstance();

        if (null === self::$instance || null !== $writer) {
            self::$instance = new self($writer);
        }
        return self::$instance;
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
        // Check first if the method is registered in any conditions
        $condition = $this->conditionsManager->getConditionByMethodName($methodName);
        if (null !== $condition) {
            self::initDebugInstance();
            if ($condition instanceof AbstractCondition) {
                $condition->setDebugInstance(self::$debugInstance);
                if ($condition->useDebugTracingForIndex()) {
                    $trace = $this->debug->findTraceKeyAndSlice($this->debug->getBackTrace(), 'function', '__call');
                    $condition->setFile($trace[0]['file'])->setLine($trace[0]['line']);
                }
            }

            // initialize the condition
            call_user_func_array(array($condition, $methodName), $args);

            $this->conditionsManager->addConditionsOperand(self::$debugInstance, $condition->evaluate());
            $this->conditionsManager->addConditionsOperator(self::$debugInstance, '&&');

            // explicitly return '$this' to enable chaining
            return $this;
        }

        // check if method name is of debug object or an extension
        $debug = null;
        if (is_callable(array($this->debug, $methodName))) {
            $debug = $this->debug;
        } else {
            foreach ($this->debugExtensions as $extension) {
                if (is_callable(array($extension, $methodName))) {
                    $debug = $extension;
                    break;
                }
            }
        }

        // a debug object has been found
        if (isset($debug)) {
            $pass = $this->conditionsManager->isPassingEvaluation(self::$debugInstance);

            // Reset the debug instance if the method name is prefixed
            if ($this->isMethodNamePrefixed($methodName)) {
                self::$debugInstance = null;
            }

            if (true === $pass) {
                return call_user_func_array(array($debug, $methodName), $args);
            }
            return;
        }

        throw new \BadFunctionCallException(sprintf(
            "Method '%s' does not exist. Please make sure it is implemented as a condition or in FdlDebug\\Debug",
            $methodName
        ));
    }

    protected function isMethodNamePrefixed($methodName)
    {
        $configs = Bootstrap::getConfigs();
        foreach ($configs['debug_prefixes'] as $prefix) {
            if (0 === strpos($methodName, $prefix)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Register the custom extensions
     * @param array $extensions
     */
    public function registerExtensions(array $extensions)
    {
        if (empty($extensions)) {
            return;
        }

        foreach ($extensions as $extension) {
            if (class_exists($extension)) {
                $extension = new $extension();
                if ($extension instanceof DebugInterface) {
                    $extension->setWriter($this->debug->getWriter());
                    $this->debugExtensions[] = $extension;
                }
            }
        }
    }

    /**
     * Initialize the writer
     * @param string $writer
     * @return \FdlDebug\Writer\WriterInterface
     */
    protected function initWriter($writer)
    {
        if (class_exists($existingWriter = __NAMESPACE__ . "\\Writer\\" . Utility::underscoreToCamelcase($writer))) {
            return new $existingWriter();
        }

        if (class_exists($writer)) {
            new $writer();
        }

        throw new \ErrorException("No writer found");
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

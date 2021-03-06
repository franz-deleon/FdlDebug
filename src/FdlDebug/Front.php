<?php
namespace FdlDebug;

use FdlDebug\Bootstrap;
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
     * The default function to search name
     * @var string
     */
    const BACKTRACE_FUNC_TO_SEARCH = '__call';

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
     * @var Writer
     */
    protected $writer;

    /**
     * Container for debug extensions
     * @var array Array containing objects
     */
    protected $debugExtensions = array();

    /**
     * The function
     * @var string
     */
    protected $backtraceFuncToSearch = self::BACKTRACE_FUNC_TO_SEARCH;

    /**
     * Protected constructor for singleton pattern
     * @param string $writer
     */
    protected function __construct($writer = null)
    {
        include __DIR__ . '/../../Bootstrapper.php';

        $configs = Bootstrap::getConfigs();

        $this->writer            = $this->initWriter($writer ?: $configs['writer']);
        $this->debug             = new Debug($this->writer);
        $this->conditionsManager = new ConditionsManager($configs['conditions'], $this->writer);
        $this->registerExtensions($configs['debug_extensions']);
    }

    /**
     * destructor
     * Run the shutdown method on each called condition
     *
     * @param void
     * @return null
     */
    public function __destruct()
    {
        $conditions = $this->conditionsManager->getImmutableCalledConditions();
        foreach ($conditions as $condition) {
            if (method_exists($condition, 'shutdown')) {
                $condition->shutdown();
            }
        }
    }

    /**
     * Retrieve the instance of this Front class
     * @param string $writer     An optional writer to pass to override the default writer.
     *                           Note that passing a writer breaks the singleton's
     *                           only one instance pattern and may impact performance as the Front
     *                           object is initialized on every call!
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
        // initialize also here so that instances passed to a variable will also relinitialize
        self::initDebugInstance();

        // Check first if the method is registered in any conditions
        $condition = $this->conditionsManager->getConditionByMethodName($methodName);
        if (null !== $condition) {
            if ($condition instanceof AbstractCondition) {
                $condition->setDebugInstance(self::$debugInstance);
                if ($condition->enableDebugTracing()) {
                    $backTrace = $this->debug->getBackTrace();
                    $trace = $this->debug->findTraceKeyAndSlice($backTrace, 'function', $this->getBacktraceFuncToSearch(), 0);

                    // bug fix for chaining procedural functions. @see Functions.php
                    if (empty($trace[0]['file']) && empty($trace[0]['line'])) {
                        $trace = $this->debug->findTraceKeyAndSlice($backTrace, 'function', self::BACKTRACE_FUNC_TO_SEARCH, 0, 1);
                    }

                    $condition->setFile($trace[0]['file']);
                    $condition->setLine($trace[0]['line']);
                    $condition->setMethod($methodName);
                }
            }

            // initialize the condition
            call_user_func_array(array($condition, $methodName), $args);

            // If its a part of $unevaluatedCallbackMethods array, reset the debug instance.
            if (in_array($methodName, $this->conditionsManager->getUnevaluatedCallbackMethods())) {
                static::resetDebugInstance();
                return;
            } else {
                $this->conditionsManager->addConditionsOperand(self::$debugInstance, $condition->evaluate());
                $this->conditionsManager->addConditionsOperator(self::$debugInstance, '&&'); //todo: implement conditions operator
                $this->conditionsManager->addCalledConditions($condition);
            }

            // explicitly return '$this' to enable chaining
            return $this;
        }

        // check if method name is of debug object, php function or of an extension
        $debug = null;
        if (is_callable(array($this->debug, $methodName))) {
            $debug = $this->debug;
        } elseif (is_callable($methodName) && $this->isFunctionName($methodName)) {
            return call_user_func_array($methodName, $args);
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
            // pass the signature to debug
            $debugTrace = $this->debug->findTraceKeyAndSlice($this->debug->getBackTrace(), 'function', self::BACKTRACE_FUNC_TO_SEARCH, 1, 0, true);
            if (!empty($debugTrace[0]['file']) && !empty($debugTrace[0]['line'])) {
                if (isset($debugTrace[0]['file']) && strpos($debugTrace[0]['file'], Bootstrap::FUNCTIONS_FILENAME) !== false) {
                    array_shift($debugTrace);
                }
                $debug->setFile($debugTrace[0]['file']);
                $debug->setLine($debugTrace[0]['line']);
            }

            $calledConditions = $this->conditionsManager->getCalledConditions();
            $pass = $this->conditionsManager->evaluateExpressions(self::$debugInstance);

            // pre processing on called conditions
            foreach ($calledConditions as $condition) {
                // the writer is pass to give the condition a chance to modify it
                $condition->preDebug();
            }

            $return = null;
            if (true === $pass) {
                $return = call_user_func_array(array($debug, $methodName), $args);
            }

            // post processing on called conditions
            foreach ($calledConditions as $condition) {
                $condition->postDebug($return, $pass);
            }

            // reset the called conditions
            $this->conditionsManager->resetCalledConditions();

            // Reset the debug instance if the method name is prefixed
            if ($this->isMethodNamePrefixed($methodName)) {
                static::resetDebugInstance();
            }

            if (!empty($return)) {
                return $return;
            }

            // if nothing passed, then return regardless to avoid an exception. This is like doing nothing
            return;
        }

        throw new \BadFunctionCallException(sprintf(
            "Method '%s' does not exist. Please make sure it is implemented as a condition or in FdlDebug\\Debug",
            $methodName
        ));
    }

    /**
     * Retrieve the conditions manager instance
     * @return \FdlDebug\Condition\ConditionsManager
     */
    public function getConditionsManager()
    {
        return $this->conditionsManager;
    }

    /**
     * Retrieve the debug instance
     * @return \FdlDebug\Debug
     */
    public function getDebug()
    {
       return $this->debug;
    }

    /**
     * Set the backtrace function to search
     * @param string $funcName
     * @return \FdlDebug\Front
     */
    public function setBacktraceFuncToSearch($funcName)
    {
        $this->backtraceFuncToSearch = $funcName;
        return $this;
    }

    /**
     * Return the backtrace function to search
     * @return string
     */
    public function getBacktraceFuncToSearch()
    {
        return $this->backtraceFuncToSearch;
    }

    /**
     * Register debug extensions
     * @param array $extensions
     */
    public function registerExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            // check for package extensions first
            $existingExtension = __NAMESPACE__ . "\\Extension\\" . Utility::underscoreToCamelcase($extension);
            if (class_exists($existingExtension)) {
                $extension = new $existingExtension();
            } elseif (class_exists($extension)) {
                $extension = new $extension();
            } else {
                continue;
            }

            $extension = new $extension();
            if ($extension instanceof DebugInterface) {
                $extension->setWriter($this->getWriter());
                $this->debugExtensions[] = $extension;
            } else {
                throw new \ErrorException(sprintf(
                    "Extension '%s' is required to implement DebugInterface",
                    get_class($extension)
                ));
            }
        }
    }

    /**
     * Initialize a debug instance if its not set
     * @param void
     * @return null
     */
    public static function initDebugInstance()
    {
        if (null === self::$debugInstance) {
            self::$debugInstance = 'dbuginstance-' . uniqid();
        }
    }

    /**
     * Resets the debug instance
     * @param void
     * @return null;
     */
    public static function resetDebugInstance()
    {
        self::$debugInstance = null;
    }

    /**
     * Retrieve the writer
     * @return \FdlDebug\Writer
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Initialize the writer
     * @param string $writer
     * @return \FdlDebug\Writer\WriterInterface
     */
    protected function initWriter($writer)
    {
        $existingWriter = __NAMESPACE__ . "\\Writer\\" . Utility::underscoreToCamelcase($writer);
        if (class_exists($existingWriter)) {
            return new $existingWriter();
        } elseif (class_exists($writer)) {
            return new $writer();
        }

        throw new \ErrorException("No writer found");
    }

    /**
     * Is the method name prefixed?
     * @param string $methodName
     * @return boolean
     */
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
     * Is this a php function name?
     * @param  string $functionName
     * @return boolean
     */
    protected function isFunctionName($functionName)
    {
        $configs = Bootstrap::getConfigs();
        if (!empty($configs['function_names']) && in_array($functionName, $configs['function_names'])) {
            return true;
        }
        return false;
    }
}

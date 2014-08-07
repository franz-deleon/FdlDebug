<?php
namespace FdlDebug\Condition;

use FdlDebug\DebugAbstract;

abstract class AbstractCondition extends DebugAbstract
{
    /**
     * Debug instance id
     * @var string
     */
    protected $debugInstance;

    /**
     * Debug instance
     * @param string $debugInstance
     * @return \FdlDebug\Condition\AbstractCondition
     */
    public function setDebugInstance($debugInstance)
    {
        $this->debugInstance = $debugInstance;
        return $this;
    }

    /**
     * Retrieve the Debug instance
     * @return string
     */
    public function getDebugInstance()
    {
        return $this->debugInstance;
    }

    /**
     * Returns the debug method's uniqe position.
     * The position is based on debug trace's concatenated file, method caller and line values
     * @return string
     */
    public function getUniquePosition()
    {
        if (false === $this->enableDebugTracing()) {
            throw new \BadMethodCallException(sprintf(
                "%s::enableDebugTracing() needs to enabled for %s",
                __CLASS__,
                __FUNCTION__
            ));
        }

        $file   = $this->getFile();
        $method = $this->getMethod();
        $line   = $this->getLine();

        if (null === $file || null === $method || null === $line) {
            throw new \ErrorException("Cannot assemble unique index");
        }

        return 'posindex-' . md5($file . ':' . $method . ':' . $line);
    }

    /**
     * Switch to enable a debug tracing call.
     * Enabling this will have setLine(), setFile() and setMethod() initialized.
     *
     * We have this switch as false for performance improvement
     * to avoid debug_backtrace being called everytime.
     *
     * @return boolean Defaults to false for performance
     * @overload
     */
    public function enableDebugTracing()
    {
        return false;
    }

    /**
     * A hook to run any code logic on conditional shutdown
     * @return void
     * @overload
     */
    public function shutdown()
    {
    }
}

<?php
namespace FdlDebug\Condition;

use FdlDebug\DebugAbstract;

abstract class AbstractCondition extends DebugAbstract
{
    /**
     * The condition's file number instance
     * @var string
     */
    protected $file;

    /**
     * The condition's line number instance
     * @var string|int
     */
    protected $line;

    /**
     * Debug instance id
     * @var string
     */
    protected $debugInstance;

    /**
     * Method name
     * @var string
     */
    protected $method;

    /**
     * The file name from __call
     * @param string $file
     * @return \FdlDebug\Condition\AbstractCondition
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Retrieve the file name
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the line number from __call
     * @param int $line
     * @return \FdlDebug\Condition\AbstractCondition
     */
    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }

    /**
     * Retrieve the line number from __call
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set the method
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Retrieve the method
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

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
     * Returns unique index string base of concatenated file and line values
     * @return string
     */
    public function getUniqueIndex()
    {
        if (false === $this->useDebugTracingForIndex()) {
            throw new \BadMethodCallException(sprintf(
                "%s::useDebugTracingForIndex() needs to enabled for %s",
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

        return $file . ':' . $method . ':' . $line;
    }

    /**
     * Switch to enable a debug tracing call.
     * Enabling this will have setLine() and setFile() initialized.
     *
     * We have this switch as false for performance improvement
     * to avoid debug_backtrace being called everytime.
     *
     * @return boolean Defaults to false for performance
     */
    public function useDebugTracingForIndex()
    {
        return false;
    }
}

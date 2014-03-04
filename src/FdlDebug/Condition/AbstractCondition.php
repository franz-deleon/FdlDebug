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


    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }

    public function getLine()
    {
        return $this->line;
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
     * Returns unique index string base of file and line data
     * @return string
     */
    public function getCreatedIndex()
    {
        if (false === $this->useDebugTracingForIndex()) {
            throw new \BadMethodCallException(sprintf(
                "%s::useDebugTracingForIndex needs to enabled for %s",
                __CLASS__,
                __FUNCTION__
            ));
        }

        return $this->getFile() . ':' . $this->getLine();
    }

    /**
     * Switch to enable a debug tracing call.
     * Enabling this will have setLine and setFile initialized.
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

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

    public function setDebugInstance($debugInstance)
    {
        $this->debugInstance = $debugInstance;
        return $this;
    }

    public function getDebugInstance()
    {
        return $this->debugInstance;
    }

    public function getCreatedIndex()
    {
        return $this->getFile() . ':' . $this->getLine();
    }

    /**
     * Swith to enable a debug tracing call.
     * Enabling this will have setLine and setFile initialized.
     *
     * We have this switch as false for performance improvement
     * to avoid debug_backtrace being called everytime.
     *
     * @return boolean
     */
    public function useDebugTracingForIndex()
    {
        return false;
    }
}

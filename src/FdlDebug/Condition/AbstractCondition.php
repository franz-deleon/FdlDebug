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
     * This Holds the created index base of file and line number
     * @var string
     */
    protected $index;

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

    public function createIndex()
    {
        $this->index = md5($trace[0]['file']) . '-' . $trace[0]['line'];
    }

    public function getCreatedIndex()
    {
        return $this->index;
    }
}

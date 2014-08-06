<?php
namespace FdlDebug\Writer;

abstract class AbstractWriter
{
    /**
     * @var bool
     */
    protected $runWrite = true;

    /**
     * Should the writer be run?
     * @param unknown $run
     */
    public function setRunWrite($run)
    {
        $this->runWrite = (bool) $run;
    }

    /**
     * Return the boolean of writer should be run
     * @return boolean
     */
    public function getRunWrite()
    {
        return $this->runWrite;
    }
}

<?php
namespace FdlDebug\Writer;

abstract class AbstractGenericOutput
{
    /**
     * List of available outputters
     * @var string
     */
    protected $outputters = array('var_dump', 'var_export', 'print_r', 'print', 'echo');

    /**
     * Default outputter
     * @var string
     */
    protected $outputter  = 'var_dump';

    /**
     * The temporary generic type to use
     * @var string
     */
    protected $tempOutputter;

    /**
     * Return the data or not?
     * @var string
     */
    protected $return = false;

    /**
     * Temporarily sets an outputer to be used
     * @param string $outputter
     * @return \FdlDebug\Writer\AbstractGenericWriter
     */
    public function setTempOutputter($outputter = null)
    {
        if (in_array($outputter, $this->outputters)) {
            $this->tempOutputter = $outputter;
        }
        return $this;
    }

    /**
     * Retrieve the outputter
     * @return string
     */
    public function getOutputter()
    {
        if (null !== $this->tempOutputter) {
            $genericType = $this->tempOutputter;
            $this->tempOutputter = null; // reset back on each use

            return $genericType;
        }
        return $this->outputter;
    }

    /**
     * Sets the outputter
     * @param string $outputter
     * @return \FdlDebug\Writer\AbstractGenericWriter
     */
    public function setOutputter($outputter)
    {
        if (in_array($outputter, $this->outputters)) {
            $this->outputter = $outputter;
        }
        return $this;
    }

    /**
     * Are we returning the result or not?
     * @return bool
     */
    public function isReturn()
    {
        if (true == $this->return) {
            return true;
        }
        return false;
    }

    /**
     * Set the return value
     * @param bool $return
     * @return \FdlDebug\Writer\AbstractGenericOutput
     */
    public function setReturn($return)
    {
        $this->return = (bool) $return;
        return $this;
    }
}

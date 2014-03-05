<?php
namespace FdlDebug\Writer;

abstract class AbstractGenericOutput
{
    /**
     * List of available outputters
     * @var string
     */
    protected $outputters = array('var_dump', 'print_r', 'print', 'echo');

    /**
     * Default outputter
     * @var string
     */
    protected $outputter  = 'var_dump';

    /**
     * The temporary generic type to use
     * @var string
     */
    protected $useGenericType;

    /**
     * Temporarily sets an outputer to be used
     * @param string $outputter
     * @return \FdlDebug\Writer\AbstractGenericWriter
     */
    public function useGenericType($outputter = null)
    {
        if (in_array($outputter, $this->outputters)) {
            $this->useGenericType = $outputter;
        }
        return $this;
    }

    /**
     * Retrieve the outputter
     * @return string
     */
    public function getOutputter()
    {
        if (null !== $this->useGenericType) {
            $genericType = $this->useGenericType;
            $this->useGenericType = null; // reset back on each use

            return $genericType;
        }
        return $this->outputter;
    }

    /**
     * Sets the outputter
     * @param string $outputter
     * @return \FdlDebug\Writer\AbstractGenericWriter
     */
    public function setOutputer($outputter)
    {
        if (in_array($outputter, $this->outputters)) {
            $this->outputter = $outputter;
        }
        return $this;
    }
}

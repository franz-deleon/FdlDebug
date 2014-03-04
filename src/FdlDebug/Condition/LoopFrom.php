<?php
namespace FdlDebug\Condition;

use FdlDebug\Bootstrap;
use FdlDebug\StdLib\Utility;

class LoopFrom extends AbstractCondition implements ConditionsInterface
{
    protected $obStart = false;

    protected $contentStorage = array();

    protected $loopFromEndKey;

    public function loopFrom($fromString)
    {
        // explicitly hack the prefixes and add a loopFromEnd
        $configs =& Bootstrap::getConfigs();
        if (!in_array('loopFromEnd', $configs['debug_prefixes'])) {
            $configs['debug_prefixes'][] = 'loopFromEnd';

            // store the last key where we have loopFromEnd so we can delete it later
            $this->loopFromEndKey = Utility::arrayLastKey($configs['debug_prefixes']);
        }

        $index = $this->getCreatedIndex();
        $this->contentStorage[$index]['expression'] = $fromString;

        if (false === $this->obStart) {
            $this->obStart = true;
            ob_flush(); // flush the output buffer first
            ob_start();
        }
    }

    public function postDebug($return = null, $passed = false)
    {
        if (true === $passed) {
            $index = $this->getCreatedIndex();
            $instance = $this->getDebugInstance();
            $this->contentStorage[$index]['content'][$instance] = $return ?: ob_get_contents();
        }

        // turn off output buffering
        if (true === $this->obStart) {
            $this->obStart = false;
            ob_clean();
        }
    }

    public function &getContentStorage()
    {
        return $this->contentStorage;
    }

    public function getLoopFromEndKey()
    {
        return $this->loopFromEndKey;
    }

    public function evaluationCallbackMethod()
    {
        return 'loopFrom';
    }

    /**
     * We return true so we store every output.
     * We do the evaluation using Extension\LoopFrom
     * @see \FdlDebug\Condition\ConditionsInterface::evaluate()
     */
    public function evaluate()
    {
        return true;
    }

    public function useDebugTracingForIndex()
    {
        return true;
    }
}

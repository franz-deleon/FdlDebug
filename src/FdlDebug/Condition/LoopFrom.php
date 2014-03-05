<?php
namespace FdlDebug\Condition;

use FdlDebug\Bootstrap;
use FdlDebug\StdLib\Utility;

class LoopFrom extends AbstractCondition implements ConditionsInterface
{
    protected $obStart = false;

    protected $contentStorage = array();

    protected $loopFromEndKey;

    public function loopFrom($fromString, $length = null)
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
        $this->contentStorage[$index]['length'] = $length;

        if (false === $this->obStart) {
            $this->obStart = true;
            ob_flush(); // flush the output buffer first
            ob_start();
        }
    }

    public function postDebug($return = null, $pass = false)
    {
        $index = $this->getCreatedIndex();
        $instance = $this->getDebugInstance();
        $this->contentStorage[$index]['content'][$instance]['value'] = $return ?: ob_get_contents();
        $this->contentStorage[$index]['content'][$instance]['pass']  = $pass;

        // turn off output buffering
        if (true === $this->obStart) {
            $this->obStart = false;
            ob_clean();
        }
    }

    public function getContentStorage()
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

    public function sliceStack()
    {
        $content = array_shift($this->contentStorage);
        $expression = $content['expression'];
        preg_match('~^(?:(?P<offset>[0-9]+)(?:st|nd|rd|th)* from )*(?P<position>start|beginning|last|end)+$~i', $expression, $matches);
        $offset   = (int) $matches['offset'];
        $position = strtolower($matches['position']);
        $contentOffset = 0;

        if ($position == 'last' || $position == 'end') {
            $contentOffset = (int) "-{$offset}";
        } else {
            $contentOffset = $offset - 1;
        }

        return array_slice($content['content'], $contentOffset, $content['length']);
    }

    /**
     * We return true so we store every output for default
     * We do the evaluation using Extension\LoopFrom
     * @see \FdlDebug\Condition\ConditionsInterface::evaluate()
     */
    public function evaluate($evaluateStack = false)
    {
        if (true === $evaluateStack) {

        }
        return true;
    }

    public function useDebugTracingForIndex()
    {
        return true;
    }
}

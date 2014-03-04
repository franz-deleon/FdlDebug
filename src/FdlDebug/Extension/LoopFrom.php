<?php
namespace FdlDebug\Extension;

use FdlDebug\Front;
use FdlDebug\DebugInterface;
use FdlDebug\Writer\WriterInterface;

class LoopFrom implements DebugInterface
{
    protected $writer;

    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * The actual evaluation of a loopFrom happens here in an extension
     * Why? Because we have to assume that we cannot get the
     * exact count of some loops e.g. streams or resources
     */
    public function loopFromEnd()
    {
        $conditionsManager = Front::i()->getConditionsManager();
        $loopFromCond   = $conditionsManager->getConditions('FdlDebug\\Condition\\LoopFrom');

        // we have the impression that all values returned by getContentStorage
        // passed the evaluation check. We just have to recheck against the loopFrom expression
        $contentStorage =& $loopFromCond->getContentStorage();

        // treat the array as a stack so each call to loopEnd from only evaluates to one
        $contentStorage = array_shift($contentStorage);


    }
}

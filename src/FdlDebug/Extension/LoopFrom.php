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
        $loopFromCond   = $conditionsManager->getConditions('FdlDebug\Condition\LoopFrom');
        $slicedStack = $loopFromCond->sliceStack();
        var_dump($slicedStack);
    }
}

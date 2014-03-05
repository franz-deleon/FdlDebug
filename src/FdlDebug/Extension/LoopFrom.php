<?php
namespace FdlDebug\Extension;

use FdlDebug\Front;
use FdlDebug\DebugInterface;
use FdlDebug\Writer\WriterInterface;
use FdlDebug\Writer\GenericOutput;

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
     * exact count of some loops e.g. streams or resources or some dynamic foreach
     */
    public function loopFromEnd($return = false)
    {
        $conditionsManager = Front::i()->getConditionsManager();
        $loopFromCond = $conditionsManager->getConditions('FdlDebug\Condition\LoopFrom');

        do {
            $slicedStack = $loopFromCond->sliceContentStack();
            if (!empty($slicedStack)) {
                foreach ($slicedStack as $key => $val) {
                    if (true === $val['passed']) {
                        if ($this->writer instanceof GenericOutput) {
                            $this->writer->useGenericType('print')->write($val['string']);
                        } else {
                            $this->writer->write($val['string']);
                        }
                    }
                }
            }
        } while (null !== $slicedStack);
    }
}

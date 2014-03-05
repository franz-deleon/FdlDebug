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
     * Flushes the stored loopFrom stack.
     * We have to assume that we cannot get the
     * exact count of some array loops e.g. streams, resources or some dynamic foreach/arrays.
     * Having this method placed outside the end of the loop solves this problem.
     *
     * @param void
     * @return null
     */
    public function loopFromFlush()
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

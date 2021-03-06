<?php
namespace FdlDebug\Condition;

use FdlDebug\Writer\GenericOutput;
use FdlDebug\Front;
use FdlDebug\StdLib;

class LoopFrom extends AbstractCondition implements ConditionsInterface
{
    /**
     * Start string identifiers
     * @var array
     */
    protected $startRegexIdentifiers = array(
        'first',
        'start',
        'beginning',
    );

    /**
     * End string identifiers
     * @var array
     */
    protected $endRegexIdentifiers = array(
        'last',
        'end',
        'ending',
    );

    /**
     * Middle identifiers
     * @var array
     */
    protected $middleRegexIdentifiers = array(
        'median',
        'middle',
        'center',
    );

    /**
     * This matches:
     * "1st from start", "2nd from end", "3rd from start", "4th from end", "start", "end"
     * @var string
     */
    protected $regexExpression = '~^(?:(?P<offset>[0-9]+)(?:st|nd|rd|th)* (?P<condition>from|before|after) )*(?P<position>%s)+$~i';

    /**
     * obStart flag is ob is started
     * @var boolean
     */
    protected static $obStart = false;

    /**
     * Content storage container
     * @var array
     */
    protected $contentStorage = array();

    /**
     * Nested loop counter
     * @var array
     */
    protected $nestedContentCounter = array();

    /**
     * Callback method
     * @param string $fromString
     * @param string $length
     */
    public function loopFrom($fromString, $length = null)
    {
        $index = $this->getUniquePosition();
        $this->contentStorage[$index]['expression'] = $fromString;
        $this->contentStorage[$index]['length'] = $length;

        $this->nestedContentCounter[$index] = $index;

        if (false === self::$obStart) {
            self::$obStart = true;
            ob_start();
        }
    }

    /**
     * Slices the content storage array one content at a time per call.
     * This is using a FIFO (first in, first out) process.
     *
     * @return array|null
     */
    public function sliceContentStack()
    {
        $content = array_shift($this->contentStorage);
        if (!empty($content['expression'])) {
            preg_match(sprintf(
                $this->regexExpression,
                implode('|', array_merge(
                    $this->startRegexIdentifiers,
                    $this->middleRegexIdentifiers,
                    $this->endRegexIdentifiers
                ))
            ), $content['expression'], $expressionMatches);

            if (!empty($expressionMatches)) {
                $offset    = (int) $expressionMatches['offset'];
                $condition = strtolower($expressionMatches['condition']);

                // check if offset is greater than count.
                $contentCount = count($content['content']);
                if ($offset > $contentCount) {
                    return;
                }

                $offset   = ($offset <= 0) ? 1 : $offset;
                $position = strtolower($expressionMatches['position']);

                $contentOffset = 0;
                if (in_array($position, $this->middleRegexIdentifiers)) {
                    $centerCount = (int) ceil($contentCount / 2) - 1;
                    if ($condition === 'before') {
                        $contentOffset = $centerCount - $offset;
                        if ($contentOffset < 0) {
                            return;
                        }
                    } elseif ($condition === 'after') {
                        $contentOffset = $centerCount + $offset;
                    } else {
                        $contentOffset = $centerCount;
                    }
                } elseif (in_array($position, $this->endRegexIdentifiers)) {
                    $contentOffset = (int) "-{$offset}"; // passing a negative val automatically searches from the end
                } else {
                    $contentOffset = $offset - 1;
                }

                return array_slice($content['content'], $contentOffset, $content['length']);
            }
        }
    }

    /**
     * todo: figure why this method is being inject in postDebug
     */
    public function loopFromNestedEnd()
    {
        $oldIndex = array_pop($this->nestedContentCounter);
        $newIndex = $oldIndex . '-' . uniqid();

        StdLib\Utility::arrayReplaceKey($oldIndex, $newIndex, $this->contentStorage);
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::preDebug()
     */
    public function preDebug()
    {
        // force the writer to not write
        $this->getWriter()->setRunWrite(false);
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::postDebug()
     */
    public function postDebug($callbackReturnVal, $passed)
    {
        $posIndex = $this->getUniquePosition();
        $instance = $this->getDebugInstance();

        $this->contentStorage[$posIndex]['content'][$instance]['string'] = $callbackReturnVal ?: ob_get_contents();
        $this->contentStorage[$posIndex]['content'][$instance]['passed'] = $passed;

        $this->getWriter()->setRunWrite(true);

        // turn off output buffering
        if (true === self::$obStart) {
            self::$obStart = false;
            ob_end_clean();
        }
    }

    /**
     * Only use for testing
     * @deprecated
     */
    public function loopFromFlush()
    {
        $this->shutdown();
    }

    /**
     * @overload
     * @see \FdlDebug\Condition\AbstractCondition::shutdown()
     */
    public function shutdown()
    {
        do {
            $writer = $this->getWriter();
            $slicedStack = $this->sliceContentStack();
            if (!empty($slicedStack)) {
                foreach ($slicedStack as $key => $val) {
                    if (true === $val['passed']) {
                        if ($writer instanceof GenericOutput) {
                            $writer->setTempOutputter('print')->write($val['string']);
                        } else {
                            $writer->write($val['string']);
                        }
                    }
                }
            }
        } while (null !== $slicedStack);
    }

    /**
     * Retrieve the content storage
     * @return array
     */
    public function getContentStorage()
    {
        return $this->contentStorage;
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::evaluationCallbackMethod()
     */
    public function evaluationCallbackMethod()
    {
        return 'loopFrom';
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\AbstractCondition::unevaluatedCallbackMethods()
     * @overload
     */
    public function unevaluatedCallbackMethods()
    {
        return array('loopFromNestedEnd', 'loopFromFlush');
    }

    /**
     * We return true so we store every output for default.
     * We do the evaluation using Extension\LoopFrom
     * @see \FdlDebug\Condition\ConditionsInterface::evaluate()
     */
    public function evaluate()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\AbstractCondition::enableDebugTracing()
     */
    public function enableDebugTracing()
    {
        return true;
    }
}

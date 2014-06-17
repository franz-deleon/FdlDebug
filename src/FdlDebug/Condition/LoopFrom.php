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
        'first', 'start', 'beginning',
    );

    /**
     * End string identifiers
     * @var array
     */
    protected $endRegexIdentifiers = array(
        'last', 'end', 'ending',
    );

    /**
     * Middle identifiers
     * @var array
     */
    protected $middleRegexIdentifiers = array(
        'median', 'middle', 'center'
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
        $index = $this->getUniqueIndex();
        $this->contentStorage[$index]['expression'] = $fromString;
        $this->contentStorage[$index]['length'] = $length;

        $this->nestedContentCounter[$index] = $index;

        if (false === self::$obStart) {
            self::$obStart = true;
            ob_flush(); // flush the output buffer first
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
        if (!empty($content)) {
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

    public function loopFromNestedEnd()
    {
        $lastIndex = array_pop($this->nestedContentCounter);
        $newIndex  = $lastIndex . '-' . uniqid();

        $this->contentStorage = StdLib\Utility::arrayReplaceKey($lastIndex, $newIndex, $this->contentStorage);

        Front::resetDebugInstance();
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
     * @see \FdlDebug\Condition\ConditionsInterface::postDebug()
     */
    public function postDebug($return = null, $pass = false)
    {
        $index    = $this->getUniqueIndex();
        $instance = $this->getDebugInstance();

        $this->contentStorage[$index]['content'][$instance]['string'] = $return ?: ob_get_contents();
        $this->contentStorage[$index]['content'][$instance]['passed'] = $pass;

        // turn off output buffering
        if (true === self::$obStart) {
            self::$obStart = false;
            ob_end_clean();
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Condition\ConditionsInterface::evaluationCallbackMethod()
     */
    public function evaluationCallbackMethod()
    {
        return array('loopFrom', 'loopFromNestedEnd');
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
     * @see \FdlDebug\Condition\AbstractCondition::useDebugTracingForIndex()
     */
    public function useDebugTracingForIndex()
    {
        return true;
    }
}

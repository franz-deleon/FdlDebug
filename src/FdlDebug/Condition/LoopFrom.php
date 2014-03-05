<?php
namespace FdlDebug\Condition;

use FdlDebug\Bootstrap;
use FdlDebug\StdLib\Utility;

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
     * This matches:
     * "1st from start", "2nd from end", "3rd from start", "4th from end", "start", "end"
     * @var string
     */
    protected $regexExpression = '~^(?:(?P<offset>[0-9]+)(?:st|nd|rd|th)* from )*(?P<position>%s)+$~i';

    /**
     * obStart flag is ob is started
     * @var boolean
     */
    protected $obStart = false;

    /**
     * Content storage container
     * @var array
     */
    protected $contentStorage = array();

    /**
     * Callback method
     * @param string $fromString
     * @param string $length
     */
    public function loopFrom($fromString, $length = null)
    {
        // explicitly hack the prefixes and add a loopFromEnd
        $configs =& Bootstrap::getConfigs();
        if (!in_array('loopFromEnd', $configs['debug_prefixes'])) {
            $configs['debug_prefixes'][] = 'loopFromFlush';
        }

        $index = $this->getUniqueIndex();
        $this->contentStorage[$index]['expression'] = $fromString;
        $this->contentStorage[$index]['length'] = $length;

        if (false === $this->obStart) {
            $this->obStart = true;
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
                implode('|', array_merge($this->startRegexIdentifiers, $this->endRegexIdentifiers))
            ), $content['expression'], $expressionMatches);

            if (!empty($expressionMatches)) {
                $offset = (int) $expressionMatches['offset'];

                // check of offset is greater than count.
                if ($offset > count($content['content'])) {
                    return;
                }

                $offset   = ($offset <= 0) ? 1 : $offset;
                $position = strtolower($expressionMatches['position']);

                $contentOffset = 0;
                if (in_array($position, $this->endRegexIdentifiers)) {
                    $contentOffset = (int) "-{$offset}"; // passing a negative val automatically searches from the end
                } else {
                    $contentOffset = $offset - 1;
                }

                return array_slice($content['content'], $contentOffset, $content['length']);
            }
        }
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
        $index = $this->getUniqueIndex();
        $instance = $this->getDebugInstance();
        $this->contentStorage[$index]['content'][$instance]['string'] = $return ?: ob_get_contents();
        $this->contentStorage[$index]['content'][$instance]['passed'] = $pass;

        // turn off output buffering
        if (true === $this->obStart) {
            $this->obStart = false;
            ob_clean();
        }
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

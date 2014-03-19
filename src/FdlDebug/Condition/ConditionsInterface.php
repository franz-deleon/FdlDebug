<?php
namespace FdlDebug\Condition;

interface ConditionsInterface
{
    /**
     * The method string to return for the conditions manager to refer to
     * for checking
     *
     * Example:
     * Range condition using loopRange() method can be accessed using the front controller by:
     * <code>
     * $front::i()->loopRange(1, 2)->pr('print me');
     * </code>
     *
     * You would then register the method condition callback method as so:
     * <code>
     * public function evaluationCallbackMethod()
     * {
     *     return 'loopRange';
     * }
     *
     * // or you can pass an array
     *
     * public function evaluationCallbackMethod()
     * {
     *     return array('loopRange', 'setRange');
     * }
     *
     * public function loopRange([$arg], [$arg2]...)
     * {
     *     // some logic
     * }
     * </code>
     *
     * @return string|array The name of the condition method
     *                      You can return an array of method name callbacks
     */
    public function evaluationCallbackMethod();

    /**
     * The method where the logic is checked if the condition passes or not
     * @return boolean
     */
    public function evaluate();

    /**
     * A hook for any post debug process/logic
     * @param $return The return value is passed automatically
     * @param $passed If the evaluation passed or not
     * @return null
     */
    public function postDebug();
}

<?php
namespace FdlDebug\Condition;

interface ConditionsInterface
{
    /**
     * The method string to return for the conditions manager to refer to
     * for checking
     *
     * Example:
     *
     * Range condition can be accessed using the front controller by:
     * <code>
     * $front::i()->setCondRange(1, 2)->pr('print me');
     * </code>
     *
     * You would register Range condition as so:
     * <code>
     * public function getMethod()
     * {
     *     return 'setCondRange';
     * }
     * </code>
     *
     * @return string The name of the condition method
     */
    public function evaluationCallbackMethod();

    /**
     * The method where the logic is checked if the condition passes or not
     * @return boolean
     */
    public function evaluate();

    /**
     * A hook for any post debug process
     * @param void
     * @return null
     */
    public function postDebug();
}

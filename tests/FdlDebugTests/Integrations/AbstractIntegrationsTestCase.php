<?php
namespace FdlDebugTests\Integrations;

abstract class AbstractIntegrationsTestCase extends \PHPUnit_Framework_TestCase
{
    public function assertOutputString()
    {
        $args   = func_get_args();
        $string = implode("\n", $args) . "\n";
        parent::expectOutputString($string);
    }
}

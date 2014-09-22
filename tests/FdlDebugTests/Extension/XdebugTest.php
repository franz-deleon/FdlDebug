<?php
namespace FdlDebugTest\Extension;

use FdlDebug\Bootstrap;
use FdlDebug\Extension\Xdebug;
use FdlDebug\Writer\GenericOutput;

/**
 * Xdebug test case.
 */
class XdebugTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Xdebug
     */
    private $Xdebug;

    /**
     * @var GenericOutput
     */
    private $writer;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->writer = new GenericOutput();
        $this->writer
            ->setOutputter('var_export')
            ->setReturn(true);

        $this->Xdebug = new Xdebug();
        $this->Xdebug->setWriter($this->writer);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Xdebug = null;
        parent::tearDown();
    }

    /**
     * Tests Xdebug->setWriter()
     * @group test1
     */
    public function testSetWriter()
    {
        $r = $this->Xdebug->setWriter($this->writer);
        $this->assertSame($this->Xdebug, $r);
    }

    /**
     * Tests Xdebug->printXdebugTracedVar()
     * @group test2
     */
    public function testPrintXdebugTracedVar()
    {
        // enable the xdebug tracing
        $_GET['XDEBUG_TRACE'] = 1;

        // overwrite the trace dir
        Bootstrap::setConfigs('xdebug', array('trace_output_dir' => __DIR__ . '/../Assets'));

        $r = $this->Xdebug->printXdebugTracedVar('cherylx');
        $this->assertRegExp("~var\([$]cherylx\) assignment~", $r);
    }

    /**
     * Tests Xdebug->printXdebugTracedVar()
     * @expectedException \ErrorException
     * @group test3
     */
    public function testPrintXdebugTracedVarReturnsExceptionOnNonString()
    {
        $this->Xdebug->printXdebugTracedVar(array());
    }

    /**
     * Tests Xdebug->printXdebugTracedVar()
     * @group test4
     */
    public function testPrintXdebugTracedVarReturnsTraceNotStarted()
    {
        $r = $this->Xdebug->printXdebugTracedVar('cherylx');
        $this->assertRegExp("~Xdebug tracing has not started. Start it first.~", $r);
    }

    /**
     * Tests Xdebug->printXdebugTracedVar()
     * @expectedException \ErrorException
     * @expectedExceptionMessage Xdebug is disabled
     * @group test5
     */
    public function testPrintXdebugTracedVarReturnsExceptionOnDisabledXdebug()
    {
        // force disable xdebug
        Bootstrap::setConfigs('xdebug_tracing_enabled', false);

        $this->Xdebug->printXdebugTracedVar('hello');
    }

    /**
     * Tests Xdebug->xdebugParseVariable()
     * @group test6
     */
    public function testXdebugParseVariable()
    {
       $traceFile = __DIR__ . '/../Assets/fdltrace.xt';
       $r = $this->Xdebug->xdebugParseVariable('cherylx', false, $traceFile);
       $r = array_pop($r);

       $this->assertInternalType('array', $r);
       $this->assertNotEmpty($r['file']);
    }
}


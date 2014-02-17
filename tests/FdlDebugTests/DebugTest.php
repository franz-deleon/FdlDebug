<?php
namespace FdlDebugTests;

use FdlDebug\Debug;

/**
 * Debug test case.
 */
class DebugTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Debug
     */
    private $Debug;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        // TODO Auto-generated DebugTest::setUp()

        $this->Debug = new Debug(/* parameters */);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated DebugTest::tearDown()
        $this->Debug = null;

        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests Debug->isXDebugEnabled()
     * @group test1
     */
    public function testIsXDebugEnabled()
    {
        $r = $this->Debug->isXDebugEnabled(/* parameters */);
        var_dump($r);
    }

    /**
     * Tests Debug->getDebugTrace()
     */
    public function testGetDebugTrace()
    {
        // TODO Auto-generated DebugTest->testGetDebugTrace()
        $this->markTestIncomplete("getDebugTrace test not implemented");

        $this->Debug->getDebugTrace(/* parameters */);
    }

    /**
     * Tests Debug->getFileTrace()
     */
    public function testGetFileTrace()
    {
        // TODO Auto-generated DebugTest->testGetFileTrace()
        $this->markTestIncomplete("getFileTrace test not implemented");

        $this->Debug->getFileTrace(/* parameters */);
    }

    /**
     * Tests Debug->loopCond()
     */
    public function testLoopCond()
    {
        // TODO Auto-generated DebugTest->testLoopCond()
        $this->markTestIncomplete("loopCond test not implemented");

        $this->Debug->loopCond(/* parameters */);
    }

    /**
     * Tests Debug->loopRange()
     */
    public function testLoopRange()
    {
        // TODO Auto-generated DebugTest->testLoopRange()
        $this->markTestIncomplete("loopRange test not implemented");

        $this->Debug->loopRange(/* parameters */);
    }

    /**
     * Tests Debug->prPredefinedVars()
     */
    public function testPrPredefinedVars()
    {
        // TODO Auto-generated DebugTest->testPrPredefinedVars()
        $this->markTestIncomplete("prPredefinedVars test not implemented");

        $this->Debug->prPredefinedVars(/* parameters */);
    }

    /**
     * Tests Debug->trace()
     */
    public function testTrace()
    {
        // TODO Auto-generated DebugTest->testTrace()
        $this->markTestIncomplete("trace test not implemented");

        $this->Debug->trace(/* parameters */);
    }

    /**
     * Tests Debug->prFiles()
     */
    public function testPrFiles()
    {
        // TODO Auto-generated DebugTest->testPrFiles()
        $this->markTestIncomplete("prFiles test not implemented");

        $this->Debug->prFiles(/* parameters */);
    }

    /**
     * Tests Debug->traceVariable()
     */
    public function testTraceVariable()
    {
        // TODO Auto-generated DebugTest->testTraceVariable()
        $this->markTestIncomplete("traceVariable test not implemented");

        $this->Debug->traceVariable(/* parameters */);
    }
}


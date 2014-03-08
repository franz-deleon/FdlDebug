<?php
namespace FdlDebugTest;

use FdlDebug\Debug;
use FdlDebug\Writer\GenericOutput;

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
        $writer = new GenericOutput();
        $writer->setOutputter('var_export')
            ->setReturn(true);
        $this->Debug = new Debug($writer);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Debug = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
    }

    /**
     * Tests Debug->pr()
     * @group test1
     */
    public function testPr()
    {
        $r = $this->Debug->pr('hello world');
        $this->assertEquals('hello world', $r);
    }

    /**
     * Tests Debug->printNow()
     * @group test2
     */
    public function testPrintNow()
    {
        $r = $this->Debug->printNow('hello earth');
        $this->assertEquals('hello earth', $r);
    }

    /**
     * Tests Debug->printGlobal()
     */
    public function testPrintGlobal()
    {
        // TODO Auto-generated DebugTest->testPrintGlobal()
        $this->markTestIncomplete("printGlobal test not implemented");

        $this->Debug->printGlobal(/* parameters */);
    }

    /**
     * Tests Debug->printBackTrace()
     */
    public function testPrintBackTrace()
    {
        // TODO Auto-generated DebugTest->testPrintBackTrace()
        $this->markTestIncomplete("printBackTrace test not implemented");

        $this->Debug->printBackTrace(/* parameters */);
    }

    /**
     * Tests Debug->printFiles()
     */
    public function testPrintFiles()
    {
        // TODO Auto-generated DebugTest->testPrintFiles()
        $this->markTestIncomplete("printFiles test not implemented");

        $this->Debug->printFiles(/* parameters */);
    }
}


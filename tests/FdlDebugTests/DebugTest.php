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
     * @group test3
     */
    public function testPrintGlobal()
    {
        $globalsList = array('SERVER', 'GET', 'POST', 'FILES', 'REQUEST', 'ENV', 'COOKIE'); //no session
        $r = $this->Debug->printGlobal(/* parameters */);
        foreach ($globalsList as $glob) {
            $this->assertRegExp(sprintf("~\'%s\' => ~", $glob), $r);
        }
    }

    /**
     * @group test4
     */
    public function testPrintGlobalSpecific()
    {
        $r = $this->Debug->printGlobal('server');
        $this->assertRegExp("~\'SHELL\' => ~", $r);
    }

    /**
     * Tests Debug->printBackTrace()
     * @group test5
     */
    public function testPrintBackTrace()
    {
        $r = $this->Debug->printBackTrace(/* parameters */);
        $this->assertRegExp("~\'END OF TRACE\'~", $r);
    }

    /**
     * Tests Debug->printFiles()
     * @group test6
     */
    public function testPrintFiles()
    {
        $r = $this->Debug->printFiles(/* parameters */);
        $this->assertRegExp("~\'END OF TRACE\'~", $r);
    }

    /**
     * @group test7
     */
    public function testPrintFilesWithVendor()
    {
        $r = $this->Debug->printFiles(true);
        $this->assertRegExp("~\'order\' => 1~", $r);
    }
}


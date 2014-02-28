<?php
namespace FdlDebugTests;

use FdlDebug;

/**
 * Front test case.
 */
class FrontTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Front
     */
    private $Front;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated FrontTest::setUp()
        $this->Front = FdlDebug\Front::i();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated FrontTest::tearDown()
        $this->Front = null;
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
     * Tests Front->__call()
     * @group test1
     */
    public function test__callPrintNow()
    {
        $this->Front->setCondBoolean(true)->pr('asdfasdf');
    }

    /**
     * @group test2
     */
    public function test__callPrintBackTrace()
    {
        $this->Front->printBackTrace();
    }

    /**
     * Tests Front->i()
     */
    public function testI()
    {
        // TODO Auto-generated FrontTest->testI()
        $this->markTestIncomplete("i test not implemented");
        $this->Front->i(/* parameters */);
    }
}


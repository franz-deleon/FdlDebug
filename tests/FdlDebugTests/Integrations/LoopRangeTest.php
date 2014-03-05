<?php
namespace FdlDebugTests\Integrations;

use FdlDebug;

/**
 * Front test case.
 */
class LoopRangeTest extends AbstractIntegrationsTestCase
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
        $this->Front = FdlDebug\Front::i();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Front = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
    }

    /**
     * @group test1
     */
    public function testLoopFromUsingFromEnd()
    {
        //$this->assertOutputString("int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopRange(2, 1)->pr($x);
        }
    }
}

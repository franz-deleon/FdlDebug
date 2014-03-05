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
    public function testLoopRange()
    {
        $this->assertOutputString("int(3)", "int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopRange(3)->pr($x);
        }
    }

    /**
     * @group test2
     */
    public function testLoopRangeWithLength()
    {
        $this->assertOutputString("int(2)", "int(3)", "int(4)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopRange(2, 3)->pr($x);
        }
    }

    /**
     * @group test3
     */
    public function testLoopRangeWithLengthAndChainOnSeparateLines()
    {
        $this->assertOutputString("int(2)", "int(3)", "int(4)");
        for ($x = 1; $x <= 5; $x++) {
            $this
                ->Front
                ->loopRange(2, 3)
                ->pr($x);
        }
    }

    /**
     * @group test4
     */
    public function testLoopRangeWithLengthOnNestedArray()
    {
        $this->assertOutputString(
            'string(5) "2nd:1"',
            'string(5) "2nd:2"',
            'string(5) "1st:3"',
            'string(5) "1st:4"',
            'string(5) "1st:5"'
        );
        for ($i = 1; $i <= 5; $i++) {
            $this->Front->loopRange(3)->pr("1st:" . $i); // only print at the 3rd loop
            for ($x = 1; $x <= 5; $x++) {
                $this->Front->loopRange(1, 2)->pr("2nd:" . $x); // print on the first and second (len=2)
            }
        }
    }

    /**
     * @group test5
     */
    public function testLoopRangeWithLengthUsingInstance()
    {
        $this->assertOutputString("int(2)", "int(3)", "int(4)");
        for ($x = 1; $x <= 5; $x++) {
            FdlDebug\Front::i()->loopRange(2, 3)->pr($x);
        }
    }

    /**
     * @group test6
     */
    public function testLoopRangeWithLengthUsingInstanceWithOutofBoundsLength()
    {
        $this->assertOutputString("int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            FdlDebug\Front::i()->loopRange(4, 10)->pr($x);
        }
    }

    /**
     * @group test7
     */
    public function testLoopRangeWithLengthAndBooleanCondAndChainsOnSeparateLines()
    {
        $this->assertOutputString("int(4)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front
                ->condBoolean($x % 2 === 0)
                ->loopRange(3, 2)
                ->pr($x);
        }
    }

    /**
     * @group test8
     */
    public function testLoopRangeWillReturnNothing()
    {
        $this->expectOutputString("");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopRange(6)->pr($x); // out of bounds here
        }
    }
}
